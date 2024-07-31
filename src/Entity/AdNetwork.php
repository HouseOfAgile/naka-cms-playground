<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Config\AdNetworkType;
use HouseOfAgile\NakaCMSBundle\Entity\AppTrait\EntityToStringTrait;
use HouseOfAgile\NakaCMSBundle\Repository\AdNetworkRepository;

#[ORM\MappedSuperclass(repositoryClass: AdNetworkRepository::class)]
class AdNetwork
{
    use EntityToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', enumType: AdNetworkType::class)]
    private ?AdNetworkType $type = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $client;

    #[ORM\Column(type: 'text', nullable: true)]
    private $scripts;

    #[ORM\OneToMany(mappedBy: 'network', targetEntity: AdBanner::class, orphanRemoval: true)]
    private $banners;

    public function __construct()
    {
        $this->banners = new ArrayCollection();
    }

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

    public function getType(): ?AdNetworkType
    {
        return $this->type;
    }

    public function setType(AdNetworkType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(?string $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, AdBanner>
     */
    public function getBanners(): Collection
    {
        return $this->banners;
    }

    public function addBanner(AdBanner $banner): static
    {
        if (!$this->banners->contains($banner)) {
            $this->banners->add($banner);
            $banner->setNetwork($this);
        }

        return $this;
    }

    public function removeBanner(AdBanner $banner): static
    {
        if ($this->banners->removeElement($banner)) {
            // set the owning side to null (unless already changed)
            if ($banner->getNetwork() === $this) {
                $banner->setNetwork(null);
            }
        }

        return $this;
    }

    public function getScripts(): ?string
    {
        return $this->scripts;
    }

    public function setScripts(?string $scripts): self
    {
        $this->scripts = $scripts;
        return $this;
    }
}
