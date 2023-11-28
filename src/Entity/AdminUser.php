<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Entity\BaseUser;
use HouseOfAgile\NakaCMSBundle\Repository\AdminUserRepository;

#[ORM\MappedSuperclass(repositoryClass: AdminUserRepository::class)]
class AdminUser extends BaseUser
{
 
}
