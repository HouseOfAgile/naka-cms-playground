<?php

namespace HouseOfAgile\NakaCMSBundle\Entity\AppTrait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Validator\Constraints as Assert;

trait AddressTrait
{
    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(groups: ['Address'])]
    private $street;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(groups: ['Address'])]
    private $zipCode;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(groups: ['Address'])]
    private $city;

    /**
     * Country name in ISO format.
     *
     * @var string
     *
     */
    #[ORM\Column(type: 'string', nullable: true, length: 3)]
    #[Assert\NotBlank(groups: ['Address'])]
    private $country;

    /**
     * @return string
     */
    public function getFullAddress()
    {
        $address = array_filter([$this->getStreet(), $this->getZipcode(), $this->getCity(), $this->getCountryName()]);

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
     * Set the value of PostcZipCodeode.
     *
     * @param string $zipCode
     *
     * @return self
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get the value of ZipCode.
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Get the value of City.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the value of City.
     *
     * @param string $city
     *
     * @return self
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
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
}
