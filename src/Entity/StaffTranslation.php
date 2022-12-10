<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

#[ORM\MappedSuperclass]
class StaffTranslation implements TranslationInterface
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected $staffRole;

    #[ORM\Column(type: 'text', nullable: true)]
    protected $presentation;

    public function __construct()
    {
        $this->locale = 'en';
    }

    public function __toString()
    {
        return sprintf(
            'StaffTranslation #%s',
            $this->id
        );
    }

    public function getId(): ?int
    {
        return $this->id;
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
            'staffRole' => $this->getStaffRole(),
            'presentation' => $this->getPresentation(),
            'locale' => $this->getLocale(),
        ];

        return $config;
    }



    public function getStaffRole(): ?string
    {
        return $this->staffRole;
    }

    public function setStaffRole(string $staffRole): void
    {
        $this->staffRole = $staffRole;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(?string $presentation): void
    {
        $this->presentation = $presentation;
    }
}
