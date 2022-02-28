<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use HouseOfAgile\NakaCMSBundle\Entity\PageGallery;
use HouseOfAgile\NakaCMSBundle\Form\PictureType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class PageGalleryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PageGallery::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            // CollectionField::new('images', 'Images')
            // ->allowAdd()
            // ->allowDelete()
            // ->setEntryIsComplex(true)
            // ->setEntryType(OfferType::class)
            // ->setFormType(PageGalleryType::class)
            // ->setFormTypeOptions([
            //     'by_reference' => 'false'
            // ]),
            CollectionField::new('images')
                ->allowAdd()
                ->allowDelete()
                ->setEntryType(PictureType::class)
        ];
    }
}
