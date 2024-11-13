<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\Page;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use HouseOfAgile\NakaCMSBundle\Admin\Field\TranslationField;
use HouseOfAgile\NakaCMSBundle\DBAL\Types\NakaPageType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PageCrudController extends AbstractCrudController implements EventSubscriberInterface
{
    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC', 'updatedAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('active');
    }

    public function configureActions(Actions $actions): Actions
    {
        $pageId = fn(Page $page): array=> [
            'page' => $page->getId(),
        ];
        $addBlockToPage = Action::new ('addBlockToPage', 'Add Block to Page', 'fa fa-plus')
            ->linkToRoute('add_block_to_page', $pageId)
            ->addCssClass('btn btn-success text-white');
        $addPageToMenu = Action::new ('addPageToMenu', 'Add Page to Menu', 'fa fa-plus')
            ->linkToRoute('add_page_to_menu', $pageId)
            ->addCssClass('btn btn-info text-white');

        $reorganizeBlocksInPage = Action::new ('configureMenu', 'backend.crud.page.action.reorganizeBlocksInPage', 'fa fa-wheel')
            ->linkToRoute('reorganize_blocks_in_page', $pageId)
            ->displayIf(static function ($entity) {
                return count($entity->getPageBlockElements()) > 1;
            })
            ->addCssClass('btn btn-success');

        return $actions
            ->add(Crud::PAGE_INDEX, $addBlockToPage)
            ->add(Crud::PAGE_INDEX, $addPageToMenu)
            ->add(Crud::PAGE_INDEX, $reorganizeBlocksInPage)
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

        $id = IdField::new ('id');

        $pageConfigurationTab = FormField::addTab('backend.form.page.pageConfigurationTab')
            ->setHelp('backend.form.page.pageConfigurationTab.help');

        $pageDetailsPanel = FormField::addPanel('backend.form.page.pageDetailsPanel')
            ->setHelp('backend.form.page.pageDetailsPanel.help');

        $pageTranslationTab = FormField::addTab('backend.form.page.mainPageTranslationsPanel')
            ->setHelp('backend.form.page.mainPageTranslationsPanel.help');

        $translations = TranslationField::new ('translations', 'Translations', $fieldsConfig)
            ->setRequired(false)
            ->hideOnIndex();

        $name = TextField::new ('name')
            ->setLabel('Name')
            ->setHelp('backend.form.page.name.help')
            ->setColumns(6);
        $slug = SlugField::new ('slug')
            ->setHelp('backend.form.page.slug.help')
            ->setTargetFieldName('name')
            ->setColumns(6);
        $active = BooleanField::new ('active')
            ->setHelp('backend.form.page.active.help')
        // ->setColumns(6)
        ;

        $pageType = ChoiceField::new ('pageType')
            ->setChoices(NakaPageType::getGuessOptions())
            ->setHelp('backend.form.page.pageType.help')
            ->setFormTypeOption('required', true);

        $pageConfigurationPanel = FormField::addPanel('backend.form.page.pageConfigurationPanel')
            ->setHelp('backend.form.page.pageConfigurationPanel.help');
        // no pageGallery for now on page level
        $pageGallery = AssociationField::new ('pageGallery', 'backend.form.page.pageGallery')
            ->setHelp('backend.form.page.pageGallery.help')
            ->setFormTypeOption('required', false);

        $pageBlockElements = AssociationField::new ('pageBlockElements', 'backend.form.page.pageBlockElements')
            ->setFormTypeOption('by_reference', false)
            ->setHelp('backend.form.page.pageBlockElements.help');
        // $category = AssociationField::new('category', 'backend.form.page.category');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $active, $name, $slug, $pageType, $pageBlockElements];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [
                $pageConfigurationTab, $pageDetailsPanel, $name, $pageType, $pageGallery,
            ];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [
                $pageConfigurationTab, $pageDetailsPanel, $name, $pageType, $pageGallery, $pageBlockElements,
                // $pageConfigurationPanel, $category,
                $pageTranslationTab, $translations,

            ];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [

                $pageConfigurationTab, $pageDetailsPanel, $active, $name, $slug, $pageType, $pageGallery, $pageBlockElements,
                // $pageConfigurationPanel, $category,
                $pageTranslationTab, $translations,

            ];
        }
    }

    public static function getSubscribedEvents(): array
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
