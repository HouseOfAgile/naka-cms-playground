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

    public function getOpeningHours(): ?array
    {
        return $this->openingHours;
    }

    public function setOpeningHours(?array $openingHours): self
    {
        $this->openingHours = $openingHours;

        return $this;
    }

    public function addOpeningHour($openingHour): self
    {
        if (!in_array($openingHour, $this->openingHours)) {
            $this->openingHours[] = $openingHour;
        }

        return $this;
    }
}
