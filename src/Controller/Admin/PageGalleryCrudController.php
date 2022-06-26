<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\PageGallery;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use HouseOfAgile\NakaCMSBundle\Form\PictureType;

class PageGalleryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PageGallery::class;
    }

    public function configureFields(string $pageName): iterable
    {

        $id = IdField::new('id');
        $name = TextField::new('name');
        $images = AssociationField::new('images')->setFormTypeOptions([
            'by_reference' => false,
        ]);
        // $images = CollectionField::new('images')
        //     ->allowAdd()
        //     ->allowDelete()
        //     ->setEntryType(PictureType::class);
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $images];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $images];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $images];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $images];
        }
    }
}
