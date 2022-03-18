<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use HouseOfAgile\NakaCMSBundle\Admin\Field\TranslationField;
use App\Entity\Page;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PageCrudController extends AbstractCrudController implements EventSubscriberInterface
{
    public static function getEntityFqcn(): string
    {
        return Page::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $fieldsConfig = [
            'title' => [
                'field_type' => TextareaType::class,
                'required' => true,
                'label' => 'Title',
            ],
            // 'text' => [
            //     'field_type' => CKEditorType::class,
            //     'required' => true,
            //     'label' => 'Текст',
            // ],
        ];

        $id = IdField::new('id');
        $slug = TextField::new('slug');
        $translations = TranslationField::new('translations', 'Translations', $fieldsConfig)
            ->setRequired(false)
            ->hideOnIndex();
        $name = TextField::new('title')->hideOnForm()->setLabel('Title')->setFormTypeOptions(
            [
                'default_locale' => 'en',
            ]
        );
        $enabled = BooleanField::new('enabled')->setLabel('is it Enabled');
        $pageGallery = AssociationField::new('pageGallery', 'admin.form.page.pageGallery');
        $pageBlockElements = AssociationField::new('pageBlockElements', 'admin.form.page.pageBlockElements')
            ->setFormTypeOption('by_reference', false);
        $category = AssociationField::new('category', 'admin.form.page.category');


        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $slug, $enabled, $pageGallery, $pageBlockElements];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $slug, $enabled, $pageGallery, $category];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $translations, $enabled, $pageGallery, $category, $pageBlockElements];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $translations, $enabled, $pageGallery, $category, $pageBlockElements];
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => 'generateSlug',
            BeforeEntityUpdatedEvent::class => 'generateSlug',
        ];
    }

    /** @internal */
    public function generateSlug($event)
    {
        /** @var Page $page */
        $page = $event->getEntityInstance();
        if (!($page instanceof Page)) {
            return;
        }
        $page->generateSlug();
    }
}
