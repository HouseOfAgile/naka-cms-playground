<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\NakaSiteSettings;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Vich\UploaderBundle\Form\Type\VichImageType;

class NakaSiteSettingsCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return NakaSiteSettings::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined(false)
            ->overrideTemplates([
                'crud/detail' => '@NakaCMS/backend/crud/naka-site-settings/detail.html.twig',
                'crud/edit' => '@NakaCMS/backend/crud/naka-site-settings/edit.html.twig',
            ])
            ->setPageTitle('detail', 'backend.nakaSiteSettings.pageTitle.detail')
            ->setPageTitle('edit', 'backend.nakaSiteSettings.pageTitle.edit')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $logoImageFile = Field::new ('logoImageFile')
            ->setHelp('backend.nakaSiteSettings.logoImageFile.help')
            ->setFormType(VichImageType::class)
            ->setLabel('backend.nakaSiteSettings.logoImageFile.label');

        $logoImagePreview = ImageField::new ('logoImage.name')
            ->setHelp('backend.nakaSiteSettings.logoImageFile.help')
            ->setTemplatePath('@NakaCMS/admin/fields/vich_image.html.twig')
            ->setLabel('backend.nakaSiteSettings.logoImage.label')
            ->setCustomOption('uploadField', 'logoImageFile');

        $logoTransparentImageFile = Field::new ('logoTransparentImageFile')
            ->setHelp('backend.nakaSiteSettings.logoTransparentImageFile.help')
            ->setFormType(VichImageType::class)
            ->setLabel('backend.nakaSiteSettings.logoTransparentImageFile.label');

        $logoTransparentImagePreview = ImageField::new ('logoTransparentImage.name')
            ->setHelp('backend.nakaSiteSettings.logoTransparentImageFile.help')
            ->setTemplatePath('@NakaCMS/admin/fields/vich_image.html.twig')
            ->setLabel('backend.nakaSiteSettings.logoTransparentImage.label')
            ->setCustomOption('uploadField', 'logoTransparentImageFile');

        $defaultBackgroundImageFile = Field::new ('defaultBackgroundImageFile')
            ->setHelp('backend.nakaSiteSettings.defaultBackgroundImageFile.help')
            ->setFormType(VichImageType::class)
            ->setLabel('backend.nakaSiteSettings.defaultBackgroundImageFile.label');

        $defaultBackgroundImagePreview = ImageField::new ('defaultBackgroundImage.name')
            ->setHelp('backend.nakaSiteSettings.defaultBackgroundImageFile.help')
            ->setTemplatePath('@NakaCMS/admin/fields/vich_image.html.twig')
            ->setLabel('backend.nakaSiteSettings.defaultBackgroundImage.label')
            ->setCustomOption('uploadField', 'defaultBackgroundImageFile');

        $updatedAt = DateTimeField::new ('updatedAt')
            ->setLabel('backend.nakaSiteSettings.updatedAt.label')
            ->hideOnForm();

        if (Crud::PAGE_EDIT === $pageName) {
            return [
                $logoImageFile,
                $logoTransparentImageFile,
                $defaultBackgroundImageFile,
                $updatedAt,
            ];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [
                $logoImagePreview,
                $logoTransparentImagePreview,
                $defaultBackgroundImagePreview,
                $updatedAt,
            ];
        }

        return [];
    }

    public function configureActions(Actions $actions): Actions
    {

        dump($actions);
        return $actions
            ->disable(Action::NEW , Action::DELETE)
            ->disable(Action::NEW , Action::INDEX)
        ;
    }

    public function index(AdminContext $context)
    {
        $url = $this->adminUrlGenerator
            ->setController(self::class)
            ->setAction('detail')
            ->setEntityId(1)
            ->generateUrl();

        return $this->redirect($url);
    }
}
