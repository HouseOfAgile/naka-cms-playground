<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Entity\AppTrait\EntityToStringTrait;
use HouseOfAgile\NakaCMSBundle\Repository\AdBannerRepository;

#[ORM\MappedSuperclass(repositoryClass: AdBannerRepository::class)]
class AdBanner
{
    use EntityToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $style;

    #[ORM\Column(type: 'string', length: 20)]
    private $slot;

    #[ORM\Column(type: 'string', length: 255)]
    private $format;

    #[ORM\Column(type: 'boolean')]
    private $responsive;

    #[ORM\ManyToOne(targetEntity: AdNetwork::class, inversedBy: 'banners')]
    #[ORM\JoinColumn(nullable: false)]
    private $network;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function setStyle(string $style): static
    {
        $this->style = $style;

        return $this;
    }

    public function getSlot(): ?string
    {
        return $this->slot;
    }

    public function setSlot(string $slot): static
    {
        $this->slot = $slot;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function isResponsive(): ?bool
    {
        return $this->responsive;
    }

    public function setResponsive(bool $responsive): static
    {
        $this->responsive = $responsive;

        return $this;
    }

    public function getNetwork(): ?AdNetwork
    {
        return $this->network;
    }

    public function setNetwork(?AdNetwork $network): static
    {
        $this->network = $network;

        return $this;
    }
}
