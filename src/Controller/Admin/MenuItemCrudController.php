<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use HouseOfAgile\NakaCMSBundle\Admin\Field\TranslationField;
use HouseOfAgile\NakaCMSBundle\DBAL\Types\NakaMenuItemType;
use HouseOfAgile\NakaCMSBundle\Form\DictItemType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MenuItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MenuItem::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $menuItemId = fn (MenuItem $menuItem): array => [
            'menuItem' => $menuItem->getId(),
        ];
        $duplicateMenuItem = Action::new('duplicateMenuItem', 'Duplicate Generator', 'fa fa-clone')
            ->linkToRoute('duplicate_menu_item', $menuItemId)
            ->addCssClass('btn btn-warning');

        return $actions
            ->add(Crud::PAGE_INDEX, $duplicateMenuItem)
            ->add(Crud::PAGE_DETAIL, $duplicateMenuItem)
            ;
    }



    public function configureFields(string $pageName): iterable
    {
        $fieldsConfig = [
            'title' => [
                'field_type' => TextareaType::class,
                'required' => true,
                'label' => 'Menu item translation',
            ],
        ];

        $id = IdField::new('id');

        $menuItemConfigurationPanel = FormField::addPanel('backend.form.menuItem.configurationPanel');
        $name = TextField::new('name');
        $translations = TranslationField::new('translations', 'Translations', $fieldsConfig)
            ->setRequired(false)
            ->hideOnIndex()
            ->setHelp('backend.form.menuItem.translations.help');
        $position = IntegerField::new('position')->setLabel('Position?')
            ->setHelp('backend.form.position.help');

        $menuItemTypePanel = FormField::addPanel('backend.form.menuItem.typePanel')
            ->setHelp('backend.form.menuItem.typePanel.help');
        $type = ChoiceField::new('type')->setChoices(NakaMenuItemType::getGuessOptions())
            ->setHelp('backend.form.menuItem.type.help');

        $page = AssociationField::new('page', 'admin.form.menuItem.page')->setColumns('col-4');
        $uri = TextField::new('uri')->setColumns('col-4');
        $route = TextField::new('route')
            ->setColumns('col-4');
        $routeParameters = CollectionField::new('routeParameters')
            ->allowAdd()
            ->allowDelete()
            ->setEntryType(DictItemType::class)
            ->showEntryLabel(false)
            ->setColumns('col-4 offset-8');



        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $type, $route, $page, $position];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $type, $route, $page, $position];
        } elseif (Crud::PAGE_NEW === $pageName || Crud::PAGE_EDIT === $pageName) {
            return [
                $menuItemConfigurationPanel, $name, $translations, $position,
                $menuItemTypePanel, $type, $route,  $page, $uri, $routeParameters,
            ];
        }
    }
}
