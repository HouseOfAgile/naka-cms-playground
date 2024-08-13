<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Entity\Address;
use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Countries;

#[ORM\MappedSuperclass(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $name = null;

    #[ORM\Column(length: 3, nullable: true)]
    protected ?string $country = null;

    #[ORM\OneToMany(mappedBy: 'city', targetEntity: Address::class, cascade: ['detach'])]
    protected Collection $addresses;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
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
            'name' => $this->getName(),
            'country' => $this->getCountry(),
        ];

        return $config;
    }

    public function __toString()
    {
        return sprintf(
            'City: #%s %s',
            $this->id,
            $this->getName(),
        );
    }
    
    /**
     * Get the value of Country.
     *
     * @return string
     */
    public function getCountryName()
    {
        return  $this->country ? Countries::getName($this->country) : 'not set';
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAdresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setCity($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getCity() === $this) {
                $address->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }
}
