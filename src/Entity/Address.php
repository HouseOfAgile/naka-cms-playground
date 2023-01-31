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

    /**
     * Country name in ISO format.
     *
     * @var string
     *
     *
     */
    #[ORM\Column(type: 'string', nullable: true, length: 3)]
    #[Assert\NotBlank(groups: ['Address'])]
    protected $country;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function __toString()
    {
        return sprintf(
            'Address: #%s %s',
            $this->id,
            $this->getFullAddress(),
        );
        
    }
    /**
     * @return string
     */
    public function getFullAddress()
    {
        // @todo add city
        $address = array_filter([$this->getPostcode(), $this->getStreet(), $this->getCountryName()]);

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

    /**
     * Get the value of Country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
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

    /**
     * Set the value of Country.
     *
     * @param string $country
     *
     * @return self
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
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
