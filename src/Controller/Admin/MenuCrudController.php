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
        $menuId = fn(Menu $menu): array => [
            'menu' => $menu->getId(),
        ];
        $configureMenu = Action::new('configureMenu', 'backend.crud.menu.action.reorganizeMenu', 'fa-solid fa-arrow-up-a-z')
            ->linkToRoute('configure_menu', $menuId)
            ->addCssClass('btn btn-success');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $actions
                ->remove(Crud::PAGE_INDEX, Action::NEW);
        }
        return $actions
            ->add(Crud::PAGE_INDEX, $configureMenu)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            // ->add(Crud::PAGE_INDEX, $viewPerformanceStrategy)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $id      = IdField::new('id');
        $name    = TextField::new('name');
        $isFixed = BooleanField::new('isFixed')
            ->setHelp('backend.form.menu.isFixed.help');

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
        return parent::configureCrud($crud)
            ->overrideTemplates([
                'crud/index' => '@NakaCMS/backend/crud/menu/index.html.twig',
                'crud/edit'  => '@NakaCMS/backend/crud/menu/edit.html.twig',
                'crud/new'   => '@NakaCMS/backend/crud/menu/new.html.twig',
            ]);
    }
}
