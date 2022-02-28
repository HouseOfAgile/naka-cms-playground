<?php

namespace HouseOfAgile\NakaCMSBundle\Entity\AppTrait;

trait DefaultTranslatableTrait
{
    /**
     * Return translation based on locale, or default translation (english here)
     * @param $entity
     * @param $field
     * @param bool $showDefault
     * @return null|string
     */
    public function getDefaultEnglishTranslation($entity, $field, $showDefault = false)
    {
        $methodName = 'get' . ucfirst($field);
        if ($entity->translate()->{$methodName}() !== null) {
            return $entity->translate()->{$methodName}();

        } else {
            if ($entity->translate('en')->{$methodName}() !== null) {
                return $entity->translate('en')->{$methodName}();
            } elseif ($entity->translate('de')->{$methodName}() !== null) {
                return $entity->translate('de')->{$methodName}();
            } else {
                return ($showDefault) ? substr(md5(rand()), 0, 7) : null;
            }
        }

    }
}
