<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\Picture;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PictureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Picture::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('createdAt');
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new ('id');

        $imagePreview = ImageField::new ('image.name')
            ->setTemplatePath('@NakaCMS/admin/fields/vich_image.html.twig');

        $imageFile = Field::new ('imageFile')->setFormType(VichImageType::class);

        $imageName = TextField::new ('image.name');
		
        $name = TextField::new ('name')
            ->setLabel('Name')
            ->setHelp('Internal name to be used in Menus and elsewhere');
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $imageName, $imagePreview];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $imagePreview];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $imageFile];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $imageFile];
        }
    }
}
