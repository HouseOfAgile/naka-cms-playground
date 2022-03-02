<?php

namespace HouseOfAgile\NakaCMSBundle\Controller;

use HouseOfAgile\NakaCMSBundle\Entity\BaseUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    protected function getUser(): ?BaseUser
    {
        return parent::getUser();
    }
}
