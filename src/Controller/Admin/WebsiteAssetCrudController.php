<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\WebsiteAsset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichFileType;

class WebsiteAssetCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WebsiteAsset::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined(true)
            ->overrideTemplate('crud/index', '@NakaCMS/backend/crud/website-asset/list.html.twig');
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        if (!$this->isGranted('ROLE_ADMIN')) {
            $actions
                ->remove(Crud::PAGE_INDEX, Action::NEW )
                ->remove(Crud::PAGE_INDEX, Action::DELETE)
                ->remove(Crud::PAGE_INDEX, Action::EDIT);
        }

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new ('id');

        $websiteAssetDetailsTab = FormField::addTab('backend.form.websiteAsset.tab.websiteAssetDetailsTab')
            ->setIcon('wheel')
            ->setHelp('backend.form.websiteAsset.tab.websiteAssetDetailsTab.help');

        $name = TextField::new ('name')
            ->setLabel('Name')
            ->setHelp('backend.form.websiteAsset.name');

        $assetDescription = TextField::new ('assetDescription')
            ->setLabel('backend.form.websiteAsset.assetDescription')
            ->setHelp('backend.form.websiteAsset.assetDescription.help');

        $assetPreview = ImageField::new ('asset.name')
            ->setLabel('backend.form.websiteAsset.preview')
            ->setTemplatePath('@NakaCMS/admin/fields/vich_file.html.twig');

        $assetFile = Field::new ('assetFile')->setFormType(VichFileType::class);

        $webSiteAssetDetailsFields = [
            $websiteAssetDetailsTab,
            FormField::addColumn(6),
            $name, $assetDescription,
            FormField::addColumn(6),
            $assetFile,
        ];
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $assetPreview, $assetDescription];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return array_merge([$id, $assetPreview, $assetDescription]);
        } elseif (Crud::PAGE_NEW === $pageName) {
            return array_merge($webSiteAssetDetailsFields);
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return array_merge($webSiteAssetDetailsFields);
        }
    }
}
