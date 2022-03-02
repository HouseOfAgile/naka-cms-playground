<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use HouseOfAgile\NakaCMSBundle\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User extends BaseUser
{
    public function __construct()
    {
    }
}
