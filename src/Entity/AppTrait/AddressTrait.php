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
    private $postcode;

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
        $address = array_filter([$this->getCity(), $this->getPostcode(), $this->getStreet(), $this->getCountryName()]);

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
