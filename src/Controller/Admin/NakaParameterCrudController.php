<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\NakaParameter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class NakaParameterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return NakaParameter::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
