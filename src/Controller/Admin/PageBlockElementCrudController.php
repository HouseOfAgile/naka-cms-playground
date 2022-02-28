<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use HouseOfAgile\NakaCMSBundle\Entity\PageBlockElement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class PageBlockElementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PageBlockElement::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');

        $page = AssociationField::new('page', 'admin.form.pageBlockElement.page');
        $staticPage = AssociationField::new('staticPage', 'admin.form.pageBlockElement.staticPage');
        $blockElement = AssociationField::new('blockElement', 'admin.form.pageBlockElement.blockElement');
        $position = IntegerField::new('position')->setLabel('Position?');


        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $page, $staticPage, $blockElement, $position];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $page, $staticPage,  $blockElement, $position];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$page, $staticPage, $blockElement, $position];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$page, $staticPage, $blockElement, $position];
        }
    }
}
