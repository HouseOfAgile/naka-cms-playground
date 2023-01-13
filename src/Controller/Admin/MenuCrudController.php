<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\Menu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MenuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Menu::class;
    }
    
    public function configureActions(Actions $actions): Actions
    {
        $menuId = fn (Menu $menu): array => [
            'menu' => $menu->getId(),
        ];
        $configureMenu = Action::new('configureMenu', 'backend.crud.menu.action.reorganizeMenu', 'fa fa-wheel')
            ->linkToRoute('configure_menu', $menuId)
            ->addCssClass('btn btn-success');

        return $actions
            ->add(Crud::PAGE_INDEX, $configureMenu)
            // ->add(Crud::PAGE_INDEX, $viewPerformanceStrategy)
        ;
    }
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');
        $name = TextField::new('name');
        $isFixed = BooleanField::new('isFixed');
        $menuItems = AssociationField::new('menuItems', 'backend.form.menu.menuItems');
        /** @var Menu $thisEntity */
        $thisEntity = $this->getContext()->getEntity()->getInstance();
        // here we check if this menu is fixed to not be able to change the name
        if ($thisEntity && $thisEntity->getIsFixed()) {
            $name->setFormTypeOption('disabled', 'disabled');
        }

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $menuItems];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $menuItems];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return $this->isGranted('ROLE_SUPER_ADMIN') ? [$name, $menuItems, $isFixed] : [$name, $menuItems];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return $this->isGranted('ROLE_SUPER_ADMIN') ? [$name, $menuItems, $isFixed] : [$name, $menuItems];
        }
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud = parent::configureCrud($crud);
        $crud
            // ->setFormThemes(
            //     [
            //         '@A2lixTranslationForm/bootstrap_4_layout.html.twig',
            //         '@EasyAdmin/crud/form_theme.html.twig',
            //         // '@FOSCKEditor/Form/ckeditor_widget.html.twig',
            //     ]
            // )
            ->overrideTemplates([
                'crud/edit' => '@NakaCMS/admin/crud/menu/form_menu.html.twig',
                'crud/new' => '@NakaCMS/admin/crud/menu/form_menu.html.twig',
            ]);

        return $crud;
    }
}
