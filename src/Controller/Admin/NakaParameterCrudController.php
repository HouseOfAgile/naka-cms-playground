<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\NakaParameter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class NakaParameterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return NakaParameter::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');
        $path = TextField::new('path')
            ->setHelp('backend.form.nakaParameter.path.help');
        $value = TextField::new('value')
            ->setHelp('backend.form.nakaParameter.value.help');
        $extraInfo = TextField::new('extraInfo')
            ->setHelp('backend.form.nakaParameter.extraInfo.help');
        $nakaParameterFields = [$path, $value, $extraInfo];

        if (Crud::PAGE_INDEX === $pageName) {
            return array_merge([$id], $nakaParameterFields);
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return array_merge([$id], $nakaParameterFields);
        } elseif (Crud::PAGE_NEW === $pageName) {
            return $nakaParameterFields;
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return $nakaParameterFields;
        }
    }


    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->showEntityActionsInlined(false)
            ->overrideTemplates([
                'crud/index' => '@NakaCMS/backend/crud/naka-parameter/index.html.twig',
                'crud/edit' => '@NakaCMS/backend/crud/naka-parameter/edit.html.twig',
                'crud/new' => '@NakaCMS/backend/crud/naka-parameter/new.html.twig',
            ]);
    }
}
