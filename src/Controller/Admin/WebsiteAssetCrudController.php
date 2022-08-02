<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\WebsiteAsset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
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
            ->overrideTemplate('crud/index', '@NakaCMS/backend/website-asset/list.html.twig');
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

        $asset = ImageField::new('asset.name')
            ->setTemplatePath('@NakaCMS/admin/fields/vich_file.html.twig');
        $assetFile = Field::new('assetFile')->setFormType(VichFileType::class);
        $assetName = TextField::new('asset.name');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $asset, $assetName];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $asset, $assetFile];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$assetFile];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$assetFile];
        }
    }
}
