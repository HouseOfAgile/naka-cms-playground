<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\AdBanner;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AdBannerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AdBanner::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('backend.adBanner.entityLabelInSingular')
            ->setEntityLabelInPlural('backend.adBanner.entityLabelInPlural')
            ->setHelp('index', 'backend.adBanner.helpIndex')
            ->setHelp('edit', 'backend.adBanner.helpEdit')
            ->setHelp('new', 'backend.adBanner.helpNew');
    }

    public function configusreFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'backend.adBanner.name')
                ->setHelp('backend.adBanner.nameHelp'),
            TextField::new('style', 'backend.adBanner.style')
                ->setHelp('backend.adBanner.styleHelp'),
            IntegerField::new('slot', 'backend.adBanner.slot')
                ->setHelp('backend.adBanner.slotHelp'),
            TextField::new('format', 'backend.adBanner.format')
                ->setHelp('backend.adBanner.formatHelp'),
            BooleanField::new('responsive', 'backend.adBanner.responsive')
                ->setHelp('backend.adBanner.responsiveHelp'),
            AssociationField::new('network', 'backend.adBanner.network')
                ->setHelp('backend.adBanner.networkHelp'),
        ];
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');

        $name = TextField::new('name')
            ->setHelp('backend.adBanner.name.help');
        $style = TextField::new('style')
            ->setHelp('backend.adBanner.style.help');
        $slot = IntegerField::new('slot')
            ->setHelp('backend.adBanner.slot.help');
        $format = TextField::new('format')
            ->setHelp('backend.adBanner.format.help');
        $responsive = BooleanField::new('responsive')
            ->setHelp('backend.adBanner.responsive.help');
        $network = AssociationField::new('network')
            ->setHelp('backend.adBanner.network.help');
        $adBannerDetailsFields = [
            $name, $style, $slot, $format, $responsive, $network,
        ];

        if (Crud::PAGE_INDEX === $pageName) {
            return array_merge([$id], $adBannerDetailsFields);
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return array_merge($adBannerDetailsFields);
        } elseif (Crud::PAGE_NEW === $pageName || Crud::PAGE_EDIT === $pageName) {
            return array_merge($adBannerDetailsFields);
        }
    }
}
