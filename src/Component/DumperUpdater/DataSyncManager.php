<?php

namespace HouseOfAgile\NakaCMSBundle\Component\DumperUpdater;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\Proxy;
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
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Yaml\Yaml;
use Vich\UploaderBundle\Handler\UploadHandler;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper as VichUploaderHelper;

/**
 * Manages data synchronization between the application and external data representations.
 * Capable of handling various formats, currently supports YAML, with plans to support additional formats in the future.
 */
class DataSyncManager
{
    use LoggerCommandTrait;

    /** @var LoggerInterface */
    protected $logger;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var string */
    protected $dataDumpDir;

    /** @var string */
    protected $assetDir;

    /** @var string */
    protected $projectDir;

    /** @var FilesystemOperator */
    protected $filesystem;

    /** @var UploadHandler */
    protected $uploadHandler;

    /** @var VichUploaderHelper */
    protected $vichUploaderHelper;

    /** @var UploaderHelper */
    protected $uploaderHelper;

    protected $appEntitiesDict = [];
    protected $entitiesIdMapping = [];
    protected $assetIdMapping = [];
    protected $appEntities = [];
    protected $assetEntities = [];
    protected $appEntitiesAliases = [];

    public function __construct(
        LoggerInterface $scrappingLogger,
        EntityManagerInterface $entityManager,
        FilesystemOperator $assetPicturesFsFilesystem,
        UploadHandler $uploadHandler,
        VichUploaderHelper $vichUploaderHelper,
        UploaderHelper $uploaderHelper,
        string $projectDir
    ) {
        $this->logger = $scrappingLogger;
        $this->entityManager = $entityManager;
        $this->filesystem = $assetPicturesFsFilesystem;
        $this->uploadHandler = $uploadHandler;
        $this->vichUploaderHelper = $vichUploaderHelper;
        $this->uploaderHelper = $uploaderHelper;
        $this->projectDir = $projectDir;
        $this->dataDumpDir = $projectDir . '/naka-cms/active/data/content';
        $this->assetDir = $projectDir . '/naka-cms/active/data/resources';
    }

    /**
     * Set the SymfonyStyle instance for this component
     *
     * @param SymfonyStyle $io
     * @return void
     */
    public function setIo(SymfonyStyle $io): void
    {
        $this->setLoaderCommandIo($io);
    }

    private function updateMappings(array $entities): void
    {
        foreach ($entities as $entityName => $entityAttributes) {
            $entityClass = 'App\\Entity\\' . ucfirst($entityName);
            $repository = $this->entityManager->getRepository($entityClass);
            $this->appEntitiesDict[$entityName] = $repository;
            $this->entitiesIdMapping[$entityName] = [];
            $this->assetIdMapping[$entityName] = [];
        }
    }

    public function setupDataSyncManager(array $appEntities, array $appEntitiesAliases, array $assetEntities): void
    {
        $this->updateMappings($assetEntities);
        $this->updateMappings($appEntities);
        $this->appEntities = $appEntities;
        $this->assetEntities = $assetEntities;
        $this->appEntitiesAliases = $appEntitiesAliases;
    }

    public function manageNakaCMS(array $appEntities, array $appEntitiesAliases, array $assetEntities, bool $dumpOrUpdate = false, bool $doNotMoveAsset = false): bool
    {

        $this->setupDataSyncManager($appEntities, $appEntitiesAliases, $assetEntities);
        $assetsSynchronized = $this->synchronizeAssets($dumpOrUpdate, $doNotMoveAsset);
        if ($assetsSynchronized) {
            $contentSynchronized = $this->synchronizeData($dumpOrUpdate);
            return $assetsSynchronized && $contentSynchronized;
        }
        return false;
    }

    /**
     * Synchronize data: execute a dump of entities defined in appEntities into YAML files 
     * or update existing YAML files into the current database.
     *
     * @param bool $dumpOrUpdate
     * @return bool
     */
    public function synchronizeData(bool $dumpOrUpdate = false): bool
    {
        foreach ($this->appEntities as $type => $appEntityAttr) {
            $repository = $this->appEntitiesDict[$type];

            $dataArray = [];
            if ($dumpOrUpdate) {
                if (!array_key_exists($type, $this->assetEntities)) {
                    foreach ($repository->findBy([], ['id' => 'ASC']) as $entityItem) {
                        $dataArray[$entityItem->getId()] = $this->dynamicDump($entityItem);
                    }
                    $this->dumpFile($dataArray, $type);
                    $this->logSuccess(sprintf('Configuration has been dumped for dynamic instances definition (%s)', $type));
                } else {
                    $this->logWarning(sprintf('Not dumping asset entity: %s', $type));
                }
            } else {
                $this->logInfo(sprintf('Updating data for entity %s', $type));

                $filesystem = new Filesystem();
                $filePath = sprintf('%s/%s.yml', $this->dataDumpDir, $type);
                if (!$filesystem->exists($filePath)) {
                    $this->logWarning(sprintf('No file for entity %s, skipping', $type));
                    continue;
                }

                $dumpedEntities = Yaml::parseFile($filePath);
                if (count($dumpedEntities) > 0) {
                    foreach ($dumpedEntities as $entityKey => $entityData) {
                        if (array_key_exists($type, $this->assetEntities)) {
                            $this->logInfo('Asset entity detected');
                            continue;
                        }

                        $entity = $repository->findOneBy(['id' => $entityKey]);

                        if (!$entity) {
                            $entityClass = 'App\\Entity\\' . ucfirst($type);
                            $entity = new $entityClass();
                            $this->logInfo(sprintf('Create Entity %s with id %s', ucfirst($type), $entityKey));
                        } else {
                            $this->logInfo(sprintf('Update Entity %s with id %s', ucfirst($type), $entityKey));
                        }

                        $this->updateEntityFromYamlData($entity, $entityData, $type);

                        $this->entityManager->persist($entity);
                        $this->entityManager->flush();
                        $this->logInfo(sprintf('Saved entity %s with id %s', $type, $entity->getId()));

                        $this->entitiesIdMapping[$type][$entityData['id']] = $entity->getId();
                    }
                    $this->logSuccess(sprintf('Updated all data for entities %s (%d entries)', $type, count($dumpedEntities)));
                } else {
                    $this->logWarning(sprintf('We have no entity %s', $type));
                }
            }
        }
        return true;
    }

    public function updateEntityFromYamlData($entity, $dataEntity, $type = null): object
    {
        try {
            $type = $type ?? $this->getShortClassName($entity);
            foreach ($dataEntity as $keyAttr => $valAttr) {
                $this->logCommand(sprintf('Working on %s', $keyAttr));
                if ($valAttr === null) {
                    $this->logWarning(sprintf('Skipping %s as value is null', $keyAttr));
                    continue;
                }

                if (is_array($valAttr) && !array_key_exists($keyAttr, $this->appEntities[$type])) {
                    foreach ($valAttr as $refId) {
                        $this->processOneToManyRelation($entity, $keyAttr, $refId);
                    }
                } else {
                    $this->processSingleValueAttribute($entity, $keyAttr, $valAttr, $type);
                }
            }
        } catch (\Throwable $th) {
            dd($entity, $dataEntity, $type, $th);
        }
        return $entity;
    }

    public function getShortClassName($entity): string
    {
        $reflection = new \ReflectionClass($entity instanceof Proxy ? get_parent_class($entity) : get_class($entity));
        return lcfirst($reflection->getShortName());
    }

    public function processSingleYamlEntry($yamlChangeSet): int
    {
        foreach ($yamlChangeSet as $entityClass => $entityData) {
            if (!array_key_exists('id', $entityData)) {
                $entity = new $entityClass();
                $this->logInfo(sprintf('Create Entity %s', ucfirst($entityClass)));
            } else {
                $repository = $this->entityManager->getRepository($entityClass);

                $entity = $repository->findOneBy(['id' => $entityData['id']]);

                $this->logInfo(sprintf('Update Entity %s with id %s', ucfirst($entityClass), $entityData['id']));
            }
            $this->updateEntityFromYamlData($entity, $entityData);
        }
        // WIP: dd($entity);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $this->logInfo(sprintf('Saved entity %s with id %s', $entityClass, $entity->getId()));
        return $entity->getId();
    }

    private function processOneToManyRelation($entity, string $keyAttr, $refId): void
    {
        if (array_key_exists($keyAttr, $this->appEntitiesAliases)) {
            $relatedEntity = is_array($this->appEntitiesAliases[$keyAttr]) ? $this->appEntitiesAliases[$keyAttr]['class'] : $this->appEntitiesAliases[$keyAttr];
            $addMethod = is_array($this->appEntitiesAliases[$keyAttr]) ? ucfirst($this->appEntitiesAliases[$keyAttr]['method']) : 'add' . ucfirst($this->appEntitiesAliases[$keyAttr]);

            $newRefId = $this->entitiesIdMapping[$relatedEntity][$refId];
            $linkedEntity = $this->appEntitiesDict[$relatedEntity]->findOneBy(['id' => $newRefId]);

            $entity->{$addMethod}($linkedEntity);
            $this->logInfo(sprintf('Added OneToMany relation from entity %s to entity %s', $entity, $linkedEntity));
        } else {
            if (in_array($keyAttr, array_keys($this->appEntitiesDict))) {
                $linkedEntity = $this->appEntitiesDict[$keyAttr]->findOneBy(['id' => $refId]);
                $addMethod = substr($keyAttr, -1) === 's' ? substr($keyAttr, 0, -1) : $keyAttr;
                $entity->{'add' . ucfirst($keyAttr)}($linkedEntity);
                $this->logInfo(sprintf('Added OneToMany relation from entity %s to entity %s', $entity, $linkedEntity));
            } else {
                $addMethod = substr($keyAttr, -1) === 's' ? substr($keyAttr, 0, -1) : $keyAttr;
                $entity->{'add' . ucfirst($addMethod)}($refId);
            }
        }
    }

    private function processSingleValueAttribute($entity, string $keyAttr, $valAttr, string $type): void
    {

        try {
            $relatedEntity = null;
            if (array_key_exists($keyAttr, $this->appEntitiesAliases)) {
                $relatedEntity = $this->appEntitiesAliases[$keyAttr];
            } elseif (array_key_exists($keyAttr, $this->appEntitiesDict)) {
                $relatedEntity = $this->appEntitiesDict[$keyAttr];
            } elseif (array_key_exists($keyAttr, $this->appEntities[$type])) {
                if (array_key_exists($this->appEntities[$type][$keyAttr], $this->appEntities)){
                    $relatedEntity = $this->appEntities[$type][$keyAttr];
                }
            }
            if ($relatedEntity) {
                $newRefId = $this->entitiesIdMapping[$relatedEntity][$valAttr];
                $linkedEntity = $this->appEntitiesDict[$relatedEntity]->findOneBy(['id' => $newRefId]);

                $entity->{'set' . ucfirst($keyAttr)}($linkedEntity);
                $this->logInfo(sprintf('Set relation from entity %s to entity %s', $entity, $linkedEntity));
            } else {
                if ($keyAttr !== 'id') {
                    if ($keyAttr == 'slug') {
                        $entity->{'set' . ucfirst($keyAttr)}($valAttr);
                    } elseif ($entity->{'get' . ucfirst($keyAttr)}() !== $valAttr) {
                        $valAttr = $this->convertToAppropriateType($entity, $keyAttr, $valAttr, $this->appEntities[$type]);
                        $entity->{'set' . ucfirst($keyAttr)}($valAttr);
                    }
                }
            }
        } catch (\Throwable $th) {
            dd($entity,  $keyAttr, $valAttr,  $type, $th);
        }
    }

    private function convertToAppropriateType($entity, string $keyAttr, $valAttr, array $entityAttributes)
    {
        if ($keyAttr == 'createdAt' || $keyAttr == 'updatedAt') {
            return new DateTime('@' . $valAttr, new DateTimeZone('Europe/Berlin'));
        }
        $reflectionClass = new \ReflectionClass($entity);
        $property = $reflectionClass->getProperty($keyAttr);
        $proptype = $property->getType();

        if ($proptype instanceof \ReflectionNamedType && enum_exists($proptype->getName())) {
            $enumClass = $proptype->getName();
            return constant("$enumClass::{$valAttr}");
        }

        switch ($entityAttributes[$keyAttr] ?? null) {
            case 'DateTime':
                return $valAttr ? new DateTime('@' . $valAttr, new DateTimeZone('Europe/Berlin')) : $valAttr;
            case 'DateTimeImmutable':
                return $valAttr ? new DateTimeImmutable('@' . $valAttr, new DateTimeZone('Europe/Berlin')) : $valAttr;
            case 'Ulid':
                return Ulid::fromString($valAttr);
            case 'Json':
                return json_decode($valAttr, true);
            case 'DynamicContent':
                return $this->updateDynamicContent($valAttr);
            default:
                return $valAttr;
        }
    }

    /**
     * Synchronize assets.
     *
     * @param bool $dumpOrUpdate
     * @param bool $doNotMoveAsset
     * @return bool
     */
    public function synchronizeAssets(bool $dumpOrUpdate = false, bool $doNotMoveAsset = false): bool
    {
        if ($dumpOrUpdate && !$doNotMoveAsset) {
            $filesystem = new Filesystem();
            $filesystem->remove($this->assetDir);
        }

        foreach ($this->assetEntities as $type => $attr) {
            $this->logSuccess(sprintf('Dumping assets for %s', $type));
            $repository = $this->appEntitiesDict[$type];
            $dataArray = [];

            if ($dumpOrUpdate) {
                try {
                    foreach ($repository->findAll() as $entityItem) {
                        $fileAttributeName = method_exists($entityItem, 'getImageFile') ? 'imageFile' : (method_exists($entityItem, 'getAssetFile') ? 'assetFile' : false);
                        if (!$fileAttributeName) {
                            throw new Exception('Unrecognized asset entity');
                        }

                        $pathAsset = $this->projectDir . '/public' . $this->vichUploaderHelper->asset($entityItem, $fileAttributeName);

                        if (!$doNotMoveAsset) {
                            $newPathAsset = $this->assetDir . '/' . basename($pathAsset);
                            $this->logInfo(sprintf('Moving asset from %s to %s', $pathAsset, $newPathAsset));
                            $filesystem->copy($pathAsset, $newPathAsset, true);
                        } else {
                            $newPathAsset = $pathAsset;
                        }

                        $dataArray[$entityItem->getId()] = $entityItem->dumpConfig();
                        $dataArray[$entityItem->getId()]['imagePath'] = $newPathAsset;
                    }
                } catch (IOExceptionInterface $exception) {
                    echo "An error occurred while copying asset at " . $exception->getPath();
                }

                $this->dumpFile($dataArray, $type);
                $this->logSuccess(sprintf('Configuration has been dumped for asset definition (%s)', $type));
            } else {
                $this->updateAssetsFromYaml($type, $repository);
            }
            $this->logSuccess(sprintf('Updated all assets of type %s', $type));
        }

        return true;
    }

    private function updateAssetsFromYaml(string $type, $repository): void
    {
        $dumpedEntities = Yaml::parseFile(sprintf('%s/%s.yml', $this->dataDumpDir, $type));

        foreach ($dumpedEntities as $keyEntity => $dataEntity) {
            $entity = $repository->findOneBy(['id' => $keyEntity]);

            if (!$entity) {
                $entityClass = 'App\\Entity\\' . ucfirst($type);
                $entity = new $entityClass();
                $this->logInfo(sprintf('Create Asset Entity %s with id %s', ucfirst($type), $keyEntity));
            } else {
                $this->logInfo(sprintf('Update Asset Entity %s with id %s', ucfirst($type), $keyEntity));
            }

            $fixtureImageFile = new File($dataEntity['imagePath']);
            $imageFilePath = $this->uploaderHelper->uploadPicture($fixtureImageFile, UploaderHelper::PAGE_PICTURE);
            $uploadedFile = new UploadedFile($imageFilePath, basename($imageFilePath), null, null, true);

            if (method_exists($entity, 'setImageFile')) {
                $entity->setImageFile($uploadedFile);
            } elseif (method_exists($entity, 'setAssetFile')) {
                $entity->setAssetFile($uploadedFile);
            }
            if (array_key_exists('name', $dataEntity) && $dataEntity['name'] != null) {
                $entity->setName($dataEntity['name']);
            }

            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            $this->logInfo(sprintf('Saved asset entity %s with id %s', $type, $entity->getId()));

            $this->entitiesIdMapping[$type][$dataEntity['id']] = $entity->getId();
        }
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
        if ($entity instanceof Proxy) {
            $entity->__load();
        }
        $reflection = new \ReflectionClass($entity instanceof Proxy ? get_parent_class($entity) : get_class($entity));
        $properties = $reflection->getProperties();

        $data = [];
        $metadata = $this->entityManager->getClassMetadata(get_class($entity));

        $ulidFormat = method_exists($entity, 'getUlidFormat') ? $entity->getUlidFormat() : 'toRfc4122';

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $getter = 'get' . ucfirst($propertyName);

            if (in_array($propertyName, ['__isInitialized__', 'translatable'])) {
                continue;
            }
            $value = method_exists($entity, $getter) ? $entity->$getter() : $property->getValue($entity);

            if ($propertyName === 'translations' && $value instanceof Collection) {
                $data[lcfirst($reflection->getShortName()) . ucfirst($propertyName)] = array_map(function ($item) {
                    return $item->getId();
                }, $value->toArray());
                continue;
            }

            $isJson = $this->isJsonProperty($property);

            if ($isJson) {
                // $data[$propertyName] = json_encode($value);
                $data[$propertyName] = $value;
                continue;
            }

            if ($value instanceof Ulid) {
                $data[$propertyName] = $ulidFormat === 'toBase32' ? $value->toBase32() : $value->toRfc4122();
                continue;
            }

            if ($value instanceof Collection && isset($metadata->associationMappings[$propertyName])) {
                $mapping = $metadata->associationMappings[$propertyName];
                if ($mapping['type'] === ClassMetadataInfo::MANY_TO_MANY && !isset($mapping['mappedBy'])) {
                    $data[$propertyName] = array_map(fn ($item) => $item->getId(), $value->toArray());
                }
            } elseif ($value instanceof Collection) {
                continue;
            } elseif (is_object($value) && method_exists($value, 'getId')) {
                if ($metadata->associationMappings[$propertyName]['type'] === ClassMetadataInfo::ONE_TO_ONE && isset($metadata->associationMappings[$propertyName]['mappedBy'])) continue;
                $data[$propertyName] = $value->getId();
            } elseif ($value instanceof \BackedEnum) {
                $data[$propertyName] = $value->value;
            } else {
                if ($value !== null) {
                    $data[$propertyName] = $value;
                }
            }
        }

        return $data;
    }

    private function isJsonProperty(ReflectionProperty $property): bool
    {
        foreach ($property->getAttributes(\Doctrine\ORM\Mapping\Column::class) as $attribute) {
            $args = $attribute->getArguments();
            if (array_key_exists('type', $args) && $args['type'] === 'json') {
                return true;
            }
        }
        return false;
    }

    private function updateDynamicContent($dataString): string
    {
        preg_match_all('!%%[\s]?\'?picture-([^\'|\||%]*)\|?([^\'|%]*)\'?[\s]?%%!', $dataString, $pictureMatches);
        if (!empty(array_filter($pictureMatches))) {
            foreach ($pictureMatches[0] as $id => $pictureMatch) {
                $oldId = $pictureMatches[1][$id];
                if (!array_key_exists($oldId, $this->entitiesIdMapping['Picture'])) {
                    $this->logWarning(sprintf('Unknown picture reference %s', $oldId));
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

    protected function dumpFile(array $arrayData, string $filename): void
    {
        $yaml = Yaml::dump($arrayData);
        file_put_contents(sprintf('%s/%s.yml', $this->dataDumpDir, $filename), $yaml);
    }
}
