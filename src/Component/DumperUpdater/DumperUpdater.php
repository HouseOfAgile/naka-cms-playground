<?php

namespace HouseOfAgile\NakaCMSBundle\Component\DumperUpdater;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use HouseOfAgile\NakaCMSBundle\Helper\LoggerCommandTrait;
use HouseOfAgile\NakaCMSBundle\Service\UploaderHelper;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use ReflectionProperty;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Yaml\Yaml;
use Vich\UploaderBundle\Handler\UploadHandler;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper as VichUploaderHelper;

class DumperUpdater
{
    use LoggerCommandTrait;

    /** @var LoggerInterface */
    protected $logger;

    /** @var EntityManagerInterface */
    protected $entityManager;

    protected $dataDumpDir;

    // The asset dump directory
    protected $assetDir;
    protected $projectDir;

    /** @var FilesystemOperator */
    protected $filesystem;

    /** @var UploadHandler */
    protected $uploadHandler;

    /** @var VichUploaderHelper */
    protected $vichUploaderHelper;

    /** @var UploaderHelper */
    protected $uploaderHelper;

    public function __construct(
        LoggerInterface $scrappingLogger,
        EntityManagerInterface $entityManager,
        FilesystemOperator $assetPicturesFsFilesystem,
        UploadHandler $uploadHandler,
        VichUploaderHelper $vichUploaderHelper,
        UploaderHelper $uploaderHelper,
        $projectDir
    ) {
        $this->logger = $scrappingLogger;
        $this->entityManager = $entityManager;
        $this->dataDumpDir = $projectDir . '/config/datadump';
        $this->assetDir = $projectDir . '/data/assets';
        $this->projectDir = $projectDir;
        $this->filesystem = $assetPicturesFsFilesystem;
        $this->uploadHandler = $uploadHandler;
        $this->vichUploaderHelper = $vichUploaderHelper;
        $this->uploaderHelper = $uploaderHelper;
    }

    /**
     * Set the symfony style for this component
     *
     * @param SymfonyStyle $io
     * @return void
     */
    public function setIo(SymfonyStyle $io)
    {
        $this->setLoaderCommandIo($io);
    }

    /**
     * SynchronizeData: execute a dump of entities defined in AppEntities into yaml files 
     * or update existing yml file into the current database
     *
     * @param array $appEntities
     * @param array $appEntitiesAliases
     * @param array $assetEntities
     * @param boolean $dumpOrUpdate
     * @return boolean
     */
    public function synchronizeData(array $appEntities, array $appEntitiesAliases, array $assetEntities, bool $dumpOrUpdate = false): bool
    {
        $appEntitiesDict = [];
        $entitiesIdMapping = [];

        foreach ($appEntities as $appEntityName => $appEntityAttributes) {
            $entityClass = 'App\\Entity\\' . ucfirst($appEntityName);
            $thisClass = new $entityClass();
            $repository = $this->entityManager->getRepository(get_class($thisClass));
            $appEntitiesDict[$appEntityName] = $repository;
            $entitiesIdMapping[$appEntityName] = [];
        }

        // if action is dump we dump, otherwise we udpate

        foreach ($appEntitiesDict as $type => $repository) {
            $entityClass = 'App\\Entity\\' . ucfirst($type);
            $thisClass = new $entityClass();
            $repository = $this->entityManager->getRepository(get_class($thisClass));
            $dataArray = [];
            if ($dumpOrUpdate) {
                if (!in_array($type, array_values($assetEntities))) {
                    // dump data
                    foreach ($repository->findAll() as $entityItem) {
                        $dataArray[$entityItem->getId()] = $entityItem->dumpConfig();
                    }
                    $this->dumpFile($dataArray, $type);
                    $this->logSuccess(sprintf('Configuration has been dumped for static instances definition (%s)', $type));
                } else {

                    $this->logWarning(sprintf('Not dumping Asset entity: %s', $type));
                }
            } else {
                // update
                $filesystem = new Filesystem();
                $filePath = sprintf('%s/%s.yml', $this->dataDumpDir, $type);
                if (!$filesystem->exists($filePath)) {
                    $this->logWarning(sprintf('We do not have a file for entity %s, skipping', $type));
                    continue;
                }
                // update or create
                $dumpedEntities = Yaml::parseFile($filePath);
                foreach ($dumpedEntities as $keyEntity => $dataEntity) {
                    $entity = $repository->findOneBy(['id' => $keyEntity]);
                    if (in_array($type, array_values($assetEntities))) {
                        $this->logInfo(sprintf('We have an asset entity'));
                        $entitiesIdMapping[$type][$dataEntity['id']] = $entity->getId();

                        continue;
                    } else {
                        if (!$entity) {
                            $entityClass = 'App\\Entity\\' . ucfirst($type);
                            $entity = new $entityClass();
                            $this->logInfo(sprintf('Create Entity %s with id %s', ucfirst($type), $keyEntity));
                        } else {
                            $this->logInfo(sprintf('Update Entity %s with id %s', ucfirst($type), $keyEntity));
                        }

                        foreach ($dataEntity as $keyAttr => $valAttr) {
                            $this->logInfo(sprintf('working on %s', $keyAttr));

                            // this is a onetomany relation
                            if (is_array($valAttr)) {
                                foreach ($valAttr as $refId) {
                                    if (in_array($keyAttr, array_keys($appEntitiesAliases))) {
                                        // We get the alias here if it is an alias
                                        if (is_array($appEntitiesAliases[$keyAttr])) {
                                            $relatedEntity = $appEntitiesAliases[$keyAttr]['class'];
                                            $addMethod = 'add' . ucfirst($appEntitiesAliases[$keyAttr]['method']);
                                        } else {
                                            $relatedEntity = $appEntitiesAliases[$keyAttr];
                                            $addMethod = 'add' . ucfirst($appEntitiesAliases[$keyAttr]);
                                        }
                                        // we get the new id if it has changed
                                        $newRefId = $entitiesIdMapping[$relatedEntity][$refId];

                                        $linkedEntity = $appEntitiesDict[$relatedEntity]->findOneBy(['id' => $newRefId]);

                                        $entity->{$addMethod}($linkedEntity);
                                        $this->logInfo(sprintf('<-> Add OneToMany from entity %s to entity %s', $entity, $linkedEntity));
                                    } else {
                                        dump($keyAttr);
                                        if (in_array($keyAttr, array_keys($appEntitiesDict))) {
                                            $linkedEntity = $appEntitiesDict[$keyAttr]->findOneBy(['id' => $refId]);
                                            $addMethod = substr($keyAttr, -1) === 's' ? substr($keyAttr, 0, -1) : $keyAttr;
                                            $entity->{'add' . ucfirst($keyAttr)}($linkedEntity);
                                            $this->logInfo(sprintf('<-> Add OneToMany from entity %s to entity %s', $entity, $linkedEntity));
                                        } else {
                                            $addMethod = substr($keyAttr, -1) === 's' ? substr($keyAttr, 0, -1) : $keyAttr;
                                            $entity->{'add' . ucfirst($addMethod)}($refId);
                                        }
                                    }
                                }
                            } else {
                                if (in_array($keyAttr, array_keys($appEntitiesDict)) || in_array($keyAttr, array_keys($appEntitiesAliases))) {
                                    // In this case we need to get the updated id
                                    if (in_array($keyAttr, array_keys($appEntitiesAliases))) {
                                        $relatedEntity = $appEntitiesAliases[$keyAttr];
                                    } else {
                                        $relatedEntity = $appEntitiesDict[$keyAttr];
                                    }
                                    if ($valAttr != null) {
                                        $newRefId = $entitiesIdMapping[$relatedEntity][$valAttr];

                                        $linkedEntity = $appEntitiesDict[$relatedEntity]->findOneBy(['id' => $newRefId]);

                                        $this->logInfo(sprintf('<-> Set link from entity %s to entity %s', $entity, $linkedEntity));
                                        $entity->{'set' . ucfirst($keyAttr)}($linkedEntity);
                                    } else {
                                        $this->logInfo(sprintf('Not setting %s as  null ', $keyAttr));
                                        $entity->{'set' . ucfirst($keyAttr)}(null);
                                    }
                                } elseif ($keyAttr != 'id') {
                                    dump($entity->{'get' . ucfirst($keyAttr)}());
                                    if ($entity->{'get' . ucfirst($keyAttr)}() !== $valAttr) {
                                        if (in_array($keyAttr, array_keys($appEntities[$type]))) {
                                            switch ($appEntities[$type][$keyAttr]) {
                                                case 'DateTime':
                                                    $valAttr = new DateTime('@' . $valAttr);
                                                    $this->logInfo(sprintf(
                                                        'Set %s:: previous: %s => new: %s',
                                                        $keyAttr,
                                                        $entity->{'get' . ucfirst($keyAttr)}() != null  ? $entity->{'get' . ucfirst($keyAttr)}()->format('Y-m-d H:i:s') :
                                                            'none',
                                                        $valAttr->format('Y-m-d H:i:s')
                                                    ));
                                                    break;
                                                default:
                                                    $this->logInfo(sprintf(
                                                        'Set %s:: previous: %s => new: %s',
                                                        $keyAttr,
                                                        $entity->{'get' . ucfirst($keyAttr)}(),
                                                        $valAttr
                                                    ));
                                                    break;
                                            }
                                        }
                                        // $isDate = !is_bool($valAttr) && $this->isTimestamp($valAttr);
                                        // $valAttr = $isDate ? new DateTime('@' . $valAttr) : $valAttr;

                                        $entity->{'set' . ucfirst($keyAttr)}($valAttr);
                                    }
                                }
                            }
                        }
                        $this->entityManager->persist($entity);
                        $this->entityManager->flush();
                        $this->logInfo(sprintf('Saving entity %s on id %s', $entity, $entity->getId()));
                    }

                    $entitiesIdMapping[$type][$dataEntity['id']] = $entity->getId();
                }
                $this->logSuccess(sprintf('We updated all data for entities %s', $type));
                dump($entitiesIdMapping);
            }
        }
        return true;
    }

    public function isTimestamp($string)
    {
        try {
            new DateTime('@' . $string);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * SynchronizeAssets: synchronize assets
     *
     * @param array $assetEntities
     * @param boolean $dumpOrUpdate
     * @return boolean
     */
    public function synchronizeAssets(array $assetEntities, bool $dumpOrUpdate = false): bool
    {
        $assetEntitiesDict = [];
        $entitiesIdMapping = [];

        foreach ($assetEntities as $assetEntity) {
            $entityClass = 'App\\Entity\\' . ucfirst($assetEntity);
            $thisClass = new $entityClass();
            $repository = $this->entityManager->getRepository(get_class($thisClass));
            $assetEntitiesDict[$assetEntity] = $repository;
            $entitiesIdMapping[$assetEntity] = [];
        }

        // we clean the assets directory in case of dump
        if ($dumpOrUpdate) {
            $filesystem = new Filesystem();
            $filesystem->remove($this->assetDir);
        }
        // if action is dump we dump, otherwise we udpate

        foreach ($assetEntitiesDict as $type => $repository) {
            $entityClass = 'App\\Entity\\' . ucfirst($type);
            $thisClass = new $entityClass();
            $repository = $this->entityManager->getRepository(get_class($thisClass));
            $dataArray = [];

            if ($dumpOrUpdate) {
                // dump
                try {

                    foreach ($repository->findAll() as $entityItem) {
                        $fileAttributeName = method_exists($entityItem, 'getImageFile') ? 'imageFile' : (method_exists($entityItem, 'getAssetFile') ? 'assetFile' : false);
                        if (!$fileAttributeName) {
                            throw new Exception('We do not recognize this asset entity');
                        }

                        $pathAsset = $this->projectDir . '/public' . $this->vichUploaderHelper->asset($entityItem, $fileAttributeName);
                        // copy asset and get path
                        $newPathAsset = $this->assetDir . '/' . basename($pathAsset);
                        $filesystem->copy(
                            $pathAsset,
                            $newPathAsset,
                            true
                        );

                        $dataArray[$entityItem->getId()] = $entityItem->dumpConfig();
                        $dataArray[$entityItem->getId()]['imagePath'] = $newPathAsset;
                    }
                } catch (IOExceptionInterface $exception) {
                    echo "An error occurred while copying asset at " . $exception->getPath();
                    dd($exception);
                }

                $this->dumpFile($dataArray, $type);
                $this->logSuccess(sprintf('Configuration has been dumped for asset definition (%s)', $type));
            } else {
                // update or create
                $dumpedEntities = Yaml::parseFile(sprintf('%s/%s.yml', $this->dataDumpDir, $type));
                // dump($dumpedEntities);

                foreach ($dumpedEntities as $keyEntity => $dataEntity) {
                    $entity = $repository->findOneBy(['id' => $keyEntity]);

                    if (!$entity) {
                        $entityClass = 'App\\Entity\\' . ucfirst($type);
                        $entity = new $entityClass();
                        $this->logInfo(sprintf('Create Asset Entity %s with id %s', ucfirst($type), $keyEntity));
                    } else {
                        $this->logInfo(sprintf('Update Asset Entity %s with id %s', ucfirst($type), $keyEntity));
                    }

                    $fixtureImageFile =  new File($dataEntity['imagePath']);


                    $imageFilePath = $this->uploaderHelper
                        ->uploadPicture($fixtureImageFile, UploaderHelper::PAGE_PICTURE);

                    $uploadedFile = new UploadedFile($imageFilePath, basename($imageFilePath), null, null, true);
                    if (method_exists($entity, 'setImageFile')) {
                        $entity->setImageFile($uploadedFile);
                    } elseif (method_exists($entity, 'setAssetFile')) {
                        $entity->setAssetFile($uploadedFile);
                    }

                    // foreach ($dataEntity as $keyAttr => $valAttr) {
                    // }
                    $this->entityManager->persist($entity);
                    $this->entityManager->flush();
                    $this->logInfo(sprintf('Saving entity %s on id %s', $entity, $entity->getId()));

                    $entitiesIdMapping[$type][$dataEntity['id']] = $entity->getId();
                }
            }
            $this->logSuccess(sprintf('We updated all assets of type %s', $type));
        }

        return true;
    }

    protected function dumpFile($arrayData, $filename)
    {
        $yaml = Yaml::dump($arrayData);
        file_put_contents(sprintf('%s/%s.yml', $this->dataDumpDir, $filename), $yaml);
    }
}
