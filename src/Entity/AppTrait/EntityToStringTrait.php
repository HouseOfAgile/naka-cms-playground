<?php

namespace HouseOfAgile\NakaCMSBundle\Entity\AppTrait;

trait EntityToStringTrait
{
    /**
     * Provides a string representation of the entity.
     *
     * @return string
     */
    public function __toString(): string
    {
        $className = (new \ReflectionClass($this))->getShortName(); // Get the short class name (without namespace)
        $id = $this->id ?? 'N/A'; // Handle cases where ID might not be set

        return sprintf(
            '%s %s #%s',
            $className,
            $this->getName() ?? 'Unnamed',
            $id
        );
    }

    /**
     * Gets the entity's name.
     *
     * This method should be implemented in the entity using this trait, or this trait can be extended
     * to include specific logic for fetching the name.
     *
     * @return string|null
     */
    abstract public function getName(): ?string;
}