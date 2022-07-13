<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
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
        $name = TextField::new('name');
        $route = TextField::new('route');
        $uri = TextField::new('uri');
        $translations = TranslationField::new('translations', 'Translations', $fieldsConfig)
        ->setRequired(false)
        ->hideOnIndex();
        $routeParameters = CollectionField::new('routeParameters')
            ->allowAdd()
            ->allowDelete()
            ->setEntryType(DictItemType::class)
            ->showEntryLabel(false);
        $type = ChoiceField::new('type')->setChoices(NakaMenuItemType::getGuessOptions());
        $page = AssociationField::new('page', 'admin.form.menuItem.page');
        $position = IntegerField::new('position')->setLabel('Position?')
        ->setHelp('backend.form.position.help');


        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $type, $route, $page, $position];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $type, $route, $page, $position];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $type, $route, $translations, $page, $uri, $routeParameters, $position];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $type, $route, $translations, $page, $uri, $routeParameters, $position];
        }
    }
}
