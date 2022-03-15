<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\Gallery;
use HouseOfAgile\NakaCMSBundle\Form\PictureType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GalleryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Gallery::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');
        $name = TextField::new('name');
        $pictures = CollectionField::new('pictures')
            ->allowAdd()
            ->allowDelete()
            ->setEntryType(PictureType::class);

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $pictures];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id,  $name, $pictures];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $pictures];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $pictures];
        }
    }
}
