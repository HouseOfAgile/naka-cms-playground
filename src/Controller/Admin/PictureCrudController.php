<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\Picture;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

class PictureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Picture::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');

        $image = ImageField::new('image.name')
            ->setTemplatePath('@NakaCMS/admin/fields/vich_image.html.twig');
        $imageFile = Field::new('imageFile')->setFormType(VichImageType::class);
        $name = TextField::new('image.name');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $image];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $image];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$imageFile];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$imageFile];
        }
    }
}
