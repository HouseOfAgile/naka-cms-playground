<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\Page;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use HouseOfAgile\NakaCMSBundle\Admin\Field\TranslationField;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PageCrudController extends AbstractCrudController implements EventSubscriberInterface
{
    public static function getEntityFqcn(): string
    {
        return Page::class;
    }


    public function configureActions(Actions $actions): Actions
    {
        $pageId = fn(Page $page): array => [
            'page' => $page->getId(),
        ];
        $addPageToMenu = Action::new('addPageToMenu', 'Add Page to Menu', 'fa fa-plus')
            ->linkToRoute('add_page_to_menu', $pageId)
            ->addCssClass('btn btn-info');
        return $actions
            ->add(Crud::PAGE_INDEX, $addPageToMenu)
            // ->add(Crud::PAGE_INDEX, $viewPerformanceStrategy)
            ;
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
        // panels can also define their icon, CSS class and help message

        $mainPageTranslationsPanel = FormField::addPanel('backend.form.page.mainPageTranslationsPanel')
            ->setHelp('backend.form.page.mainPageTranslationsPanel.help');
        $translations = TranslationField::new('translations', 'Translations', $fieldsConfig)
            ->setRequired(false)
            ->hideOnIndex();
        $name = TextField::new('title')->hideOnForm()->setLabel('Title')->setFormTypeOptions(
            [
                'default_locale' => 'en',
            ]
        );
        $enabled = BooleanField::new('enabled')->setLabel('is it Enabled')
            ->hideOnIndex()
            ->hideOnDetail()
            ->hideOnForm();
        $pageConfigurationPanel = FormField::addPanel('backend.form.page.pageConfigurationPanel')
            ->setHelp('backend.form.page.pageConfigurationPanel.help');
        // no pageGallery for now on page level
            $pageGallery = AssociationField::new('pageGallery', 'backend.form.page.pageGallery')
        ->setHelp('backend.form.page.pageGallery.help');
        ;
        $pageBlockElements = AssociationField::new('pageBlockElements', 'backend.form.page.pageBlockElements')
            ->setFormTypeOption('by_reference', false)
            ->setHelp('backend.form.page.pageBlockElements.help');
            ;
        $category = AssociationField::new('category', 'backend.form.page.category');


        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $slug, $enabled, $pageBlockElements];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $slug, $enabled, $category];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $mainPageTranslationsPanel, $translations, $pageConfigurationPanel, $category, $pageBlockElements, $enabled];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $mainPageTranslationsPanel, $translations, $pageConfigurationPanel, $category, $pageBlockElements, $enabled];
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
