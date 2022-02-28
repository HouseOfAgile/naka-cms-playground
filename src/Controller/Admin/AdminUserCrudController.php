<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use HouseOfAgile\NakaCMSBundle\Entity\AdminUser;
use HouseOfAgile\NakaCMSBundle\Entity\BaseUser;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class AdminUserCrudController extends BaseUserCrudController
{

    public static function getEntityFqcn(): string
    {
        return AdminUser::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('AdminUser')
            ->setEntityLabelInPlural('AdminUsers')
            ->setSearchFields(['firstname', 'email', 'roles', 'id'])
            ->setPaginatorPageSize(30);
    }

}
