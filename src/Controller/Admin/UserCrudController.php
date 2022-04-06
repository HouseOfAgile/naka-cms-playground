<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\User;
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
            ->setEntityLabelInSingular(sprintf('%s User', $this->applicationName))
            ->setEntityLabelInPlural(sprintf('%s Users', $this->applicationName))
            ->overrideTemplate('crud/index', '@NakaCMS/naka/backend/user/list.html.twig');

        return $crud;
    }
}
