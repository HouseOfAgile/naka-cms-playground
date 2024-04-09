<?php

namespace HouseOfAgile\NakaCMSBundle\Component\Translation;

use Doctrine\ORM\EntityManagerInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;

class TranslationManager
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $generalLogger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $generalLogger;
    }

    /**
     * Updates translations for entities implementing TranslatableInterface.
     *
     * @param TranslatableInterface $entity The entity whose translations are to be updated.
     * @param array $translationData An array where each key is a translatable attribute name
     *        and each value is an associative array containing:
     *        - 'targetLang': IETF language tag (e.g., 'en-US') for the target translation.
     *        - 'updated': The updated text for this translation.
     *        Example:
     *        [
     *          'name' => ['targetLang' => 'fr', 'updated' => 'Nom actualisé'],
     *          'shortDescription' => ['targetLang' => 'fr', 'updated' => 'Description brève actualisée'],
     *        ]
     *        The method updates the entity's translations for the specified language and attributes.
     *        It logs errors if any setter method is missing or if an exception occurs.
     *
     * @throws \InvalidArgumentException If the entity does not implement TranslatableInterface.
     */

    public function updateTranslations($entity, array $translationData): void
    {
        if (!$entity instanceof TranslatableInterface) {
            throw new \InvalidArgumentException('The entity must implement TranslatableInterface.');
        }

        foreach ($translationData as $attr => $details) {
            try {
                $this->updateTranslation($entity, $attr, $details['updated'], $details['targetLang']);
            } catch (\Exception $e) {
                $this->logger->error("An error occurred while updating the translation for '{$attr}': {$e->getMessage()}");
            }
        }
    }

    /**
     * Updates a single translation for a given entity.
     *
     * @param object $entity The entity that contains the translations.
     * @param string $attribute The attribute to update.
     * @param string $updatedText The new text for the translation.
     * @param string $targetLang The target language code.
     * @throws \InvalidArgumentException If the entity does not implement TranslatableInterface.
     */
    public function updateTranslation($entity, string $attribute, string $updatedText, string $targetLang): void
    {
        if (!$entity instanceof TranslatableInterface) {
            throw new \InvalidArgumentException('The entity must implement TranslatableInterface.');
        }

        try {
            $setterMethod = 'set' . ucfirst($attribute);
            $translation = $entity->translate($targetLang, false);

            if (method_exists($translation, $setterMethod)) {
                $translation->$setterMethod($updatedText);
            } else {
                $this->logger->error("The method {$setterMethod} does not exist on the translation entity.");
                return;
            }

            $entity->mergeNewTranslations();
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $this->logger->info("TM: Translation for '{$attribute}' in {$targetLang} updated to '{$updatedText}' successfully.");
        } catch (\Exception $e) {
            $this->logger->error("An error occurred while updating the translation for '{$attribute}': {$e->getMessage()}");
        }
    }

    public function getTranslatableAttributes($translationClass): array
    {
        $reflectionClass = new ReflectionClass($translationClass);
        $properties = $reflectionClass->getProperties();
        $attributeNames = [];

        foreach ($properties as $property) {
            if (!$property->isStatic()) {
                $attributeNames[] = $property->getName();
            }
        }
        $excludeKeys = ["id", "locale", "translatable"];

        $filteredAttributes = array_diff($attributeNames, $excludeKeys);

        return $filteredAttributes;
    }

    /**
     * Undocumented function
     *
     * @param object $entity The entity that contains the translations.
     * @param [type] $translationAttributes
     * @param [type] $allLocales
     * @return array
     */
    public function calculateMissingTranslations(
        $entity,
        $translationAttributes,
        $allLocales
    ): array {
        $existingTranslations = [];
        foreach ($allLocales as $locale) {
            foreach ($translationAttributes as $translationAttribute) {
                $existingTranslations[$locale][$translationAttribute] = $entity->translate($locale)->{'get' . ucfirst($translationAttribute)}();
            }
        }
        return $existingTranslations;
    }
}
