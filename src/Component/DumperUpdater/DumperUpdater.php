<?php

namespace HouseOfAgile\NakaCMSBundle\Component\DumperUpdater;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Exception;
use HouseOfAgile\NakaCMSBundle\Helper\LoggerCommandTrait;
use HouseOfAgile\NakaCMSBundle\Service\UploaderHelper;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use ReflectionProperty;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Yaml\Yaml;
use Vich\UploaderBundle\Handler\UploadHandler;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper as VichUploaderHelper;

/**
 * @deprecated This class is deprecated and will be removed in a future version. 
 * Use DataSyncManager instead.
 */

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
    protected $appEntitiesDict;
    protected $entitiesIdMapping;

    // use to track id change for assets
    protected $assetIdMapping;

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
        $this->dataDumpDir = $projectDir . '/naka-cms/active/data/content';
        $this->assetDir = $projectDir . '/naka-cms/active/data/resources';
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

    private function updateMappings(array $appEntities)
    {
        foreach ($appEntities as $appEntityName => $appEntityAttributes) {
            $entityClass = 'App\\Entity\\' . ucfirst($appEntityName);
            $thisClass = new $entityClass();
            $repository = $this->entityManager->getRepository(get_class($thisClass));
            $this->appEntitiesDict[$appEntityName] = $repository;
            $this->entitiesIdMapping[$appEntityName] = [];
            $this->assetIdMapping[$appEntityName] = [];
        }
    }

    public function manageNakaCMS(array $appEntities, array $appEntitiesAliases, array $assetEntities, bool $dumpOrUpdate = false): bool
    {
        $this->updateMappings($assetEntities);
        $this->updateMappings($appEntities);
        $assetSynchronized = $this->synchronizeAssets($assetEntities, $dumpOrUpdate);
        if ($assetSynchronized) {
            $contentSynchronized = $this->synchronizeData($appEntities, $appEntitiesAliases, $assetEntities, $dumpOrUpdate);
            return $assetSynchronized && $contentSynchronized;
        } else {
            return false;
        }
    }

    /**
     * SynchronizeData: execute a dump of entities defined in AppEntities into yaml files 
     * or update existing yaml file into the current database
     *
     * @param array $appEntities
     * @param array $appEntitiesAliases
     * @param array $assetEntities
     * @param boolean $dumpOrUpdate
     * @return boolean
     */
    public function synchronizeData(array $appEntities, array $appEntitiesAliases, array $assetEntities, bool $dumpOrUpdate = false): bool
    {
        foreach ($appEntities as $type => $appEntityAttr) {
            $repository = $this->appEntitiesDict[$type];

            $dataArray = [];
            if ($dumpOrUpdate) {
                if (!in_array($type, array_keys($assetEntities))) {
                    // dump data
                    foreach ($repository->findBy([], ['id' => 'ASC']) as $entityItem) {
                        $dataArray[$entityItem->getId()] = $this->dynamicDump($entityItem);
                    }
                    $this->dumpFile($dataArray, $type);
                    $this->logSuccess(sprintf('Configuration has been dumped for static instances definition (%s)', $type));
                } else {

                    $this->logWarning(sprintf('Not dumping Asset entity: %s', $type));
                }
            } else {
                // update
                $this->logInfo(sprintf('Working on entity %s', $type));

                $filesystem = new Filesystem();
                $filePath = sprintf('%s/%s.yml', $this->dataDumpDir, $type);
                if (!$filesystem->exists($filePath)) {
                    $this->logWarning(sprintf('We do not have a file for entity %s, skipping', $type));
                    continue;
                }
                // update or create
                $dumpedEntities = Yaml::parseFile($filePath);
                foreach ($dumpedEntities as $keyEntity => $dataEntity) {
                    if (in_array($type, array_keys($assetEntities))) {
                        $this->logInfo(sprintf('We have an asset entity'));
                        // we already have the new refId for assets
                        continue;
                    } else {
                        $entity = $repository->findOneBy(['id' => $keyEntity]);

                        if (!$entity) {
                            $entityClass = 'App\\Entity\\' . ucfirst($type);
                            $entity = new $entityClass();
                            $this->logInfo(sprintf('Create Entity %s with id %s', ucfirst($type), $keyEntity));
                        } else {
                            $this->logInfo(sprintf('Update Entity %s with id %s', ucfirst($type), $keyEntity));
                        }

                        foreach ($dataEntity as $keyAttr => $valAttr) {
                            $this->logCommand(sprintf('working on %s', $keyAttr));

                            // this is a onetomany relation
                            if (is_array($valAttr) && !in_array($keyAttr, array_keys($appEntities[$type]))) {
                                foreach ($valAttr as $refId) {
                                    if (in_array($keyAttr, array_keys($appEntitiesAliases))) {
                                        // We get the alias here if it is an alias
                                        if (is_array($appEntitiesAliases[$keyAttr])) {
                                            $relatedEntity = $appEntitiesAliases[$keyAttr]['class'];
                                            $addMethod = ucfirst($appEntitiesAliases[$keyAttr]['method']);
                                        } else {
                                            $relatedEntity = $appEntitiesAliases[$keyAttr];
                                            $addMethod = 'add' . ucfirst($appEntitiesAliases[$keyAttr]);
                                        }
                                        // we get the new id if it has changed
                                        $newRefId = $this->entitiesIdMapping[$relatedEntity][$refId];

                                        $linkedEntity = $this->appEntitiesDict[$relatedEntity]->findOneBy(['id' => $newRefId]);

                                        $entity->{$addMethod}($linkedEntity);
                                        $this->logInfo(sprintf('<-> Add OneToMany from entity %s to entity %s', $entity, $linkedEntity));
                                    } else {
                                        if (in_array($keyAttr, array_keys($this->appEntitiesDict))) {
                                            $linkedEntity = $this->appEntitiesDict[$keyAttr]->findOneBy(['id' => $refId]);
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
                                if (in_array($keyAttr, array_keys($this->appEntitiesDict)) || in_array($keyAttr, array_keys($appEntitiesAliases))) {
                                    // In this case we need to get the updated id
                                    if (in_array($keyAttr, array_keys($appEntitiesAliases))) {
                                        $relatedEntity = $appEntitiesAliases[$keyAttr];
                                    } else {
                                        $relatedEntity = $this->appEntitiesDict[$keyAttr];
                                    }
                                    if ($valAttr != null) {
                                        $newRefId = $this->entitiesIdMapping[$relatedEntity][$valAttr];

                                        $linkedEntity = $this->appEntitiesDict[$relatedEntity]->findOneBy(['id' => $newRefId]);

                                        $this->logInfo(sprintf('<-> Set link from entity %s to entity %s', $entity, $linkedEntity));
                                        $entity->{'set' . ucfirst($keyAttr)}($linkedEntity);
                                    } else {
                                        $this->logInfo(sprintf('Not setting %s as  null ', $keyAttr));
                                        $entity->{'set' . ucfirst($keyAttr)}(null);
                                    }
                                } elseif ($keyAttr != 'id') {
                                    // if key is slug, we do not try to getSlug form entity as it is not yet generated
                                    if ($keyAttr == 'slug') {
                                        $entity->{'set' . ucfirst($keyAttr)}($valAttr);
                                    } elseif ($entity->{'get' . ucfirst($keyAttr)}() !== $valAttr) {
                                        if (in_array($keyAttr, array_keys($appEntities[$type]))) {
                                            switch ($appEntities[$type][$keyAttr]) {
                                                case 'DateTime':
                                                    if ($valAttr) {
                                                        $valAttr = new DateTime('@' . $valAttr, new DateTimeZone('Europe/Berlin'));
                                                        $this->logInfo(sprintf(
                                                            'Set %s:: previous: %s => new: %s',
                                                            $keyAttr,
                                                            $entity->{'get' . ucfirst($keyAttr)}() != null  ? $entity->{'get' . ucfirst($keyAttr)}()->format('Y-m-d H:i:s') :
                                                                'none',
                                                            $valAttr->format('Y-m-d H:i:s')
                                                        ));
                                                    }
                                                    break;
                                                case 'DateTimeImmutable':
                                                    if ($valAttr) {
                                                        $valAttr = new DateTimeImmutable('@' . $valAttr, new DateTimeZone('Europe/Berlin'));
                                                        $this->logInfo(sprintf(
                                                            'Set %s:: previous: %s => new: %s',
                                                            $keyAttr,
                                                            $entity->{'get' . ucfirst($keyAttr)}() != null  ? $entity->{'get' . ucfirst($keyAttr)}()->format('Y-m-d H:i:s') :
                                                                'none',
                                                            $valAttr->format('Y-m-d H:i:s')
                                                        ));
                                                    }
                                                    break;
                                                case 'array':
                                                    $valAttr = $valAttr;
                                                    break;
                                                case 'Ulid':
                                                    $valAttr = Ulid::fromString($valAttr);
                                                    break;
                                                case 'Json':
                                                    $valAttr = json_decode($valAttr, true);
                                                    break;
                                                case 'DynamicContent':
                                                    $valAttr = $this->updateDynamicContent($valAttr);
                                                    break;
                                                case 'Enum' || 'enum':
                                                    // $valAttr = constant(ucfirst($keyAttr).'::tryFrom(\''.$valAttr.'\')');
                                                    $valAttr = constant($valAttr);
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

                                        $entity->{'set' . ucfirst($keyAttr)}($valAttr);
                                    }
                                }
                            }
                        }
                        $this->entityManager->persist($entity);
                        $this->entityManager->flush();
                        $this->logInfo(sprintf('Saving entity %s on id %s', $entity, $entity->getId()));
                    }

                    $this->entitiesIdMapping[$type][$dataEntity['id']] = $entity->getId();
                }
                $this->logSuccess(sprintf('We updated all data for entities %s', $type));

                // dump all entities for debug
                // dump($this->entitiesIdMapping);
            }
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
    public function synchronizeAssets($assetEntities, bool $dumpOrUpdate = false): bool
    {
        // we clean the assets directory in case of dump
        if ($dumpOrUpdate) {
            $filesystem = new Filesystem();
            $filesystem->remove($this->assetDir);
        }
        // if action is dump we dump, otherwise we udpate

        foreach ($assetEntities as $type => $attr) {
            $this->logSuccess(sprintf('Dumping assets for %s', $type));
            $entityClass = 'App\\Entity\\' . ucfirst($type);
            $repository = $this->appEntitiesDict[$type];
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
                        $this->logInfo(sprintf(
                            'moving asset from %s to %s',
                            $pathAsset,
                            $newPathAsset
                        ));
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
                    $this->logInfo(sprintf('Asset File is in %s', $dataEntity['imagePath']));

                    $fixtureImageFile =  new File($dataEntity['imagePath']);


                    $imageFilePath = $this->uploaderHelper
                        ->uploadPicture($fixtureImageFile, UploaderHelper::PAGE_PICTURE);

                    $uploadedFile = new UploadedFile($imageFilePath, basename($imageFilePath), null, null, true);
                    if (method_exists($entity, 'setImageFile')) {
                        $entity->setImageFile($uploadedFile);
                    } elseif (method_exists($entity, 'setAssetFile')) {
                        $entity->setAssetFile($uploadedFile);
                    }
                    if (array_key_exists('name', $dataEntity) && $dataEntity['name'] != null) {
                        $entity->setName($dataEntity['name']);
                    }

                    // foreach ($dataEntity as $keyAttr => $valAttr) {
                    // }
                    $this->entityManager->persist($entity);
                    $this->entityManager->flush();
                    $this->logInfo(sprintf('Saving entity %s on id %s', $entity, $entity->getId()));
                    // if ($entity->getId() != $dataEntity['id']) {
                    //     die('asdasd');
                    //     $this->assetIdMapping[$type];
                    // }

                    $this->entitiesIdMapping[$type][$dataEntity['id']] = $entity->getId();
                }
            }
            $this->logSuccess(sprintf('We updated all assets of type %s', $type));
        }

        return true;
    }

    /**
     * Dynamically dumps entity configuration.
     *
     * @param object $entity The entity instance to dump.
     * @return array
     * @throws \ReflectionException
     */
    public function dynamicDump($entity): array
    {
        $reflection = new \ReflectionClass($entity);
        $properties = $reflection->getProperties();

        $data = [];
        $metadata = $this->entityManager->getClassMetadata(get_class($entity));

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $getter = 'get' . ucfirst($propertyName);


            if (method_exists($entity, $getter)) {
                $value = $entity->$getter();
            } else {
                $value = $property->getValue($entity);
            }

            // KNP translatable specifics
            if ($value instanceof TranslatableInterface) continue;

            // Deal with isinitianlized, remove them for now
            if ($propertyName === '__isInitialized__') continue;

            if ($value instanceof Collection && isset($metadata->associationMappings[$propertyName])) {
                $mapping = $metadata->associationMappings[$propertyName];

                // Check if the collection is the owning side of a ManyToMany relation
                if ($mapping['type'] === ClassMetadataInfo::MANY_TO_MANY && !isset($mapping['mappedBy'])) {
                    $data[$propertyName] = array_map(function ($item) {
                        return $item->getId();
                    }, $value->toArray());
                }
            } elseif ($value instanceof Collection) {
                // Handle OneToMany or non-owning ManyToMany by ignoring or handling differently
                // } elseif ($value instanceof \DateTimeInterface) {
                // $data[$propertyName] = $value->format('Y-m-d H:i:s');
            } elseif (is_object($value) && method_exists($value, 'getId')) {
                $data[$propertyName] = $value->getId();
            } else {
                if ($value != null) {
                    $data[$propertyName] = $value;
                }
            }
        }

        return $data;
    }

    private function updateDynamicContent($dataString)
    {
        preg_match_all('!%%[\s]?\'?picture-([^\'|\||%]*)\|?([^\'|%]*)\'?[\s]?%%!', $dataString, $pictureMatches);
        if (!empty(array_filter($pictureMatches))) {
            foreach ($pictureMatches[0] as $id => $pictureMatch) {

                $oldId = $pictureMatches[1][$id];
                if (!in_array($oldId, array_keys($this->entitiesIdMapping['Picture']))) {
                    $this->logWarning(sprintf('We have an unknown picture ref %s', $oldId));
                    dump($pictureMatches);
                    return $dataString;
                }

                $newId = $this->entitiesIdMapping['Picture'][$oldId];
                $matchRegexp = sprintf(
                    '!%%%%[\s]?\'?picture-%s%s\'?[\s]?%%%%!',
                    $pictureMatches[1][$id],
                    !empty($pictureMatches[2][$id]) ? '\|' . $pictureMatches[2][$id] : ''
                );
                $dataString = preg_replace(
                    $matchRegexp,
                    sprintf(
                        '%%%% \'picture-%s%s\' %%%%',
                        $newId,
                        !empty($pictureMatches[2][$id]) ? '\|' . $pictureMatches[2][$id] : ''
                    ),
                    $dataString
                );
            }
        }
        return $dataString;
    }


    protected function dumpFile($arrayData, $filename)
    {
        $yaml = Yaml::dump($arrayData);
        file_put_contents(sprintf('%s/%s.yml', $this->dataDumpDir, $filename), $yaml);
    }
}
