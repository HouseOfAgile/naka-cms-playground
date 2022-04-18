<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\PageBlockElement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class PageBlockElementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PageBlockElement::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');

        $page = AssociationField::new('page', 'backend.crud.pageBlockElement.page');
        $relatedPage = TextareaField::new('relatedPage', 'backend.crud.pageBlockElement.relatedPage')
            ->setTemplatePath('@NakaCMS/backend_fields/page_block_element/field_related_page.html.twig');
        $blockElement = AssociationField::new('blockElement', 'backend.crud.pageBlockElement.blockElement');
        $position = IntegerField::new('position')->setLabel('Position?');


        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $relatedPage, $blockElement, $position];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $page,  $blockElement, $position];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$page, $blockElement, $position];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$page, $blockElement, $position];
        }
    }
}
