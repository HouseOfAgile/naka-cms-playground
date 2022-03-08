<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use HouseOfAgile\NakaCMSBundle\Admin\Field\TranslationField;
use App\Entity\StaticPage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
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
                'field_type' => TextareaType::class,
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
