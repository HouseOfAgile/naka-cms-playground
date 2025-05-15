<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\AdminUser;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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

    public function configureFields(string $pageName): iterable
    {
        $fieldsFromBaseUser = parent::configureFields($pageName);

        $uuid = TextField::new('uuid');


        $roles = ChoiceField::new('roles')->setChoices([
            'Content Editor' => 'ROLE_EDITOR',
            'Administrator' => 'ROLE_ADMIN',
            'Super Administrator' => 'ROLE_SUPER_ADMIN',
        ])->allowMultipleChoices()
            // ->renderAsBadges()
            ->setHelp('backend.form.adminUser.roles.help')
            ->setPermission('ROLE_SUPER_ADMIN')
            ;

        // $fieldsFromBaseUser = array_merge($fieldsFromBaseUser,[$roles]);
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $fieldsFromBaseUser = array_merge($fieldsFromBaseUser, [$roles]);
        }



        if (Crud::PAGE_INDEX === $pageName) {
            return array_merge($fieldsFromBaseUser, [$uuid]);
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return array_merge($fieldsFromBaseUser);
        } elseif (Crud::PAGE_NEW === $pageName) {
            return array_merge($fieldsFromBaseUser);
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return array_merge($fieldsFromBaseUser);
        }
    }
}
