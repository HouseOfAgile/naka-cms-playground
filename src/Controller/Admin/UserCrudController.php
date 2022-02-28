<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use HouseOfAgile\NakaCMSBundle\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class UserCrudController extends BaseUserCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

  
    public function configureCrud(Crud $crud): Crud
    {
        $crud = parent::configureCrud($crud);
        $crud
        ->setEntityLabelInSingular('Spitzmuehle User')
        ->setEntityLabelInPlural('Spitzmuehle Users')
        ->overrideTemplate('crud/index', 'naka/backend/user/list.html.twig');

        return $crud;
    }
}
