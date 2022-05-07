<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Entity\AppTrait\DefaultTranslatableTrait;
use HouseOfAgile\NakaCMSBundle\Repository\StaffRepository;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\MappedSuperclass(repositoryClass=StaffRepository::class)
 */
class Staff implements TranslatableInterface, SluggableInterface
{
    use TranslatableTrait;
    use SluggableTrait;
    use DefaultTranslatableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $businessRole;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return sprintf(
            'Staff #%s %s',
            $this->id,
            substr($this->getFullName(), 0, 39)
        );
    }

    /**
     * dumpConfig: return array with main config
     *
     * @return array
     */
    public function dumpConfig(): array
    {
        $config =  [
            'id' => $this->id,
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'businessRole' => $this->getBusinessRole(),
            'isActive' => $this->getIsActive(),
            'staffTranslations' => array_map(function ($st) {
                return $st->getId();
            }, $this->getTranslations()->toArray()),
        ];

        return $config;
    }

    public function getFullName(): ?string
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }

    /**
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['firstName', 'lastName'];
    }

    public function getStaffRole()
    {
        return $this->getDefaultEnglishTranslation($this, 'staffRole');
    }

    public function getPresentation()
    {
        return $this->getDefaultEnglishTranslation($this, 'presentation');
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBusinessRole(): ?string
    {
        return $this->businessRole;
    }

    public function setBusinessRole(string $businessRole): self
    {
        $this->businessRole = $businessRole;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
