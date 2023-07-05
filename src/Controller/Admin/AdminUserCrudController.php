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

        // @todo Move roles definitions to some configuration file
        $rolesSuperAdmin = ChoiceField::new('roles')
            ->setChoices([
                'Content Editor' => 'ROLE_EDITOR',
                'Administrator' => 'ROLE_ADMIN',
                'Super Administrator' => 'ROLE_SUPER_ADMIN',
            ])
            ->allowMultipleChoices()
            ->setPermission('ROLE_SUPER_ADMIN');

        $roles = ChoiceField::new('roles')->setChoices([
            'Content Edditor' => 'ROLE_EDITOR',
            'Administrator' => 'ROLE_ADMIN',
        ])->allowMultipleChoices()
            // ->renderAsBadges()
            ->setHelp('backend.form.adminUser.roles.help');

        // $fieldsFromBaseUser = array_merge($fieldsFromBaseUser,[$roles]);
        $newFields = [];


        if (Crud::PAGE_INDEX === $pageName) {
            return array_merge($fieldsFromBaseUser, [$uuid]);
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return array_merge($fieldsFromBaseUser, $newFields);
        } elseif (Crud::PAGE_NEW === $pageName) {
            if ($this->isGranted('ROLE_SUPER_ADMIN')) {
                return array_merge($fieldsFromBaseUser, [$rolesSuperAdmin]);
            } else {
                return array_merge($fieldsFromBaseUser, [$roles]);
            }
        } elseif (Crud::PAGE_EDIT === $pageName) {
            if ($this->isGranted('ROLE_SUPER_ADMIN')) {
                return array_merge($fieldsFromBaseUser, [$rolesSuperAdmin]);
            } else {
                return array_merge($fieldsFromBaseUser, [$roles]);
            }
        }
    }
}
