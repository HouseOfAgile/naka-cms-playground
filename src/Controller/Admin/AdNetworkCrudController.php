<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\AdNetwork;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use HouseOfAgile\NakaCMSBundle\Config\AdNetworkType;

class AdNetworkCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AdNetwork::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('backend.adNetwork.entityLabelInSingular')
            ->setEntityLabelInPlural('backend.adNetwork.entityLabelInPlural')
            ->setHelp('index', 'backend.adNetwork.helpIndex')
            ->setHelp('edit', 'backend.adNetwork.helpEdit')
            ->setHelp('new', 'backend.adNetwork.helpNew');
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');

        $name = TextField::new('name')
            ->setHelp('backend.adNetwork.name.help');
        $client = TextField::new('client')
            ->setHelp('backend.adNetwork.client.help');

        $type = ChoiceField::new('type')
            ->setHelp('backend.adNetwork.type.help')
            ->setChoices(AdNetworkType::cases());

        $scripts = TextareaField::new('scripts', 'backend.adNetwork.scripts')
            ->setHelp('backend.adNetwork.scripts.help');
        $banners = CollectionField::new('banners', 'backend.adNetwork.banners')
            ->setHelp('backend.adNetwork.banners.help')
            ->onlyOnDetail();

        $adNetworkDetailsFields = [
            $name, $client, $type, $scripts, $banners,
        ];

        if (Crud::PAGE_INDEX === $pageName) {
            return array_merge([$id], $adNetworkDetailsFields);
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return array_merge($adNetworkDetailsFields);
        } elseif (Crud::PAGE_NEW === $pageName || Crud::PAGE_EDIT === $pageName) {
            return array_merge($adNetworkDetailsFields);
        }
    }
}
