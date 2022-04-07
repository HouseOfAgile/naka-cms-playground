<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use HouseOfAgile\NakaCMSBundle\DBAL\Types\NakaMenuItemType;
use HouseOfAgile\NakaCMSBundle\Form\DictItemType;

class MenuItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MenuItem::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');
        $name = TextField::new('name');
        $route = TextField::new('route');
        $uri = TextField::new('uri');
        $routeParameters = CollectionField::new('routeParameters')
            ->allowAdd()
            ->allowDelete()
            ->setEntryType(DictItemType::class)
            ->showEntryLabel(false);
        $type = ChoiceField::new('type')->setChoices(NakaMenuItemType::getGuessOptions());
        $page = AssociationField::new('page', 'admin.form.menuItem.page');
        $position = IntegerField::new('position')->setLabel('Position?');


        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $type, $route, $page, $position];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $type, $route, $page, $position];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $type, $route, $page, $uri, $routeParameters, $position];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $type, $route, $page, $uri, $routeParameters, $position];
        }
    }
}
