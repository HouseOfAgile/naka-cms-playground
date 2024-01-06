<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\City;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(groups: ['Address'])]
    protected $street;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(groups: ['Address'])]
    protected $postcode;

    /**
     * @var City
     */
    #[ORM\ManyToOne(targetEntity: City::class, inversedBy: 'addresses', cascade: ['persist'])]
    protected City $city;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function __toString()
    {
        return sprintf(
            'Address: #%s',
            $this->id,
            // $this->getFullAddress(),
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
            'street' => $this->getStreet(),
            'postcode' => $this->getPostcode(),
        ];

        if ($this->getCity() != null) {
            $config['city'] = $this->getCity()->getId();
        }
        return $config;
    }

    /**
     * @return string
     */
    public function getFullAddress()
    {
        // @todo add city
        $address = array_filter([$this->getStreet(), $this->getPostcode(), $this->getCity() ?: null, $this->getCity() ? $this->getCity()->getCountryName() : 'not set']);
        // $address = [];

        return $address ? implode(', ', $address) : '';
    }

    /**
     * Get the value of Street.
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set the value of Street.
     *
     * @param string $street
     *
     * @return self
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Set the value of Postcode.
     *
     * @param string $postcode
     *
     * @return self
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Get the value of Postcode.
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }
}
