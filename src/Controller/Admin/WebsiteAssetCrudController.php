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
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

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
                ->remove(Crud::PAGE_INDEX, Action::NEW)
                ->remove(Crud::PAGE_INDEX, Action::DELETE)
                ->remove(Crud::PAGE_INDEX, Action::EDIT);
        }

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');


        $websiteAssetDetailsTab = FormField::addTab('backend.form.websiteAsset.tab.websiteAssetDetailsTab')
            ->setIcon('wheel')
            ->setHelp('backend.form.websiteAsset.tab.websiteAssetDetailsTab.help');

		$name = TextField::new('name')
		->setLabel('Name')
		->setHelp('Internal name to be used in Menus and elsewhere');

        $asset = ImageField::new('asset.name')
            ->setTemplatePath('@NakaCMS/admin/fields/vich_file.html.twig');
        $assetFile = Field::new('assetFile')->setFormType(VichFileType::class);
        $assetName = TextField::new('asset.name');


        $webSiteAssetDetailsFields = [
            $websiteAssetDetailsTab, $name, $assetFile,
        ];
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $asset, $assetName];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
			return array_merge([$id, $asset], $webSiteAssetDetailsFields);
        } elseif (Crud::PAGE_NEW === $pageName) {
			return array_merge($webSiteAssetDetailsFields);
        } elseif (Crud::PAGE_EDIT === $pageName) {
			return array_merge($webSiteAssetDetailsFields);
        }
    }
}
