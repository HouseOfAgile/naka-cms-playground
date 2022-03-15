<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use HouseOfAgile\NakaCMSBundle\Admin\Field\TranslationField;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setFormThemes(
                [
                    '@A2lixTranslationForm/bootstrap_4_layout.html.twig',
                    '@EasyAdmin/crud/form_theme.html.twig',
                    // '@FOSCKEditor/Form/ckeditor_widget.html.twig',
                ]
            );
    }

    public function configureFields(string $pageName): iterable
    {
        $fieldsConfig = [
            'name' => [
                'field_type' => TextareaType::class,
                'required' => true,
                'label' => 'Name',
            ],
            // 'text' => [
            //     'field_type' => CKEditorType::class,
            //     'required' => true,
            //     'label' => 'Текст',
            // ],
        ];

        return [
            TranslationField::new('translations', 'Translations', $fieldsConfig)
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('name')->hideOnForm()->setLabel('Name')->setFormTypeOptions(
                [
                    'default_locale' => 'en',
                ]
            ),
            AssociationField::new('pages', 'admin.form.category.pages'),


        ];
    }


}
