<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\StaticPage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\TextEditorType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use HouseOfAgile\NakaCMSBundle\Admin\Field\TranslationField;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class StaticPageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StaticPage::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fieldsConfig = [
            'title' => [
                'field_type' => TextareaType::class,
                'required' => true,
                'label' => 'Title',
            ],
            'content' => [
                'field_type' => CKEditorType::class,
                'required' => true,
                'label' => 'Content',
            ],
            // 'text' => [
            //     'field_type' => CKEditorType::class,
            //     'required' => true,
            //     'label' => 'Текст',
            // ],
        ];

        $id = IdField::new('id');
        $translations = TranslationField::new('translations', 'Translations', $fieldsConfig)
            ->setRequired(false)
            ->hideOnIndex();
        $name = TextField::new('name')
            ->setLabel('Name')
            ->setHelp('Internal name to be used in Menus and elsewhere');
        $pageBlockElements = AssociationField::new('pageBlockElements', 'admin.form.page.pageBlockElements');


        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $pageBlockElements];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $pageBlockElements];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $translations, $pageBlockElements];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $translations, $pageBlockElements];
        }
    }
}
