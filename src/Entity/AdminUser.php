<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use HouseOfAgile\NakaCMSBundle\Repository\AdminUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdminUserRepository::class)
 */
class AdminUser extends BaseUser
{
 
}
