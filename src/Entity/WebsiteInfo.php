<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableMethodsTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatablePropertiesTrait;

#[ORM\MappedSuperclass]
class WebsiteInfo implements TranslatableInterface
{
    use TranslatablePropertiesTrait;
    use TranslatableMethodsTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(type: 'json', nullable: true)]
    protected $openingHours = [];
    
    public function __toString()
    {
        return sprintf(
            'WebsiteInfo #%s',
            $this->id,
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
            'openingHours' => json_encode($this->getOpeningHours()),
            'websiteInfoTranslations' => array_map(function ($pt) {
                return $pt->getId();
            }, $this->getTranslations()->toArray()),
        ];

        return $config;
    }

    public function getOpeningHours(): ?array
    {
        return $this->openingHours;
    }

    public function setOpeningHours(?array $openingHours): self
    {
        $this->openingHours = $openingHours;

        return $this;
    }

}
