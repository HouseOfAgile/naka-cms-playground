<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\NakaSiteSettings;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Vich\UploaderBundle\Form\Type\VichImageType;

class NakaSiteSettingsCrudController extends AbstractCrudController
{
    protected AdminUrlGenerator $adminUrlGenerator;
    protected EntityManagerInterface $entityManager;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityManager = $entityManager;
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

        $updatedAt = DateTimeField::new ('updatedAt')
            ->setLabel('backend.nakaSiteSettings.updatedAt.label')
            ->hideOnForm();

        if (Crud::PAGE_EDIT === $pageName) {
            return [
                $updatedAt,
                $logoImageFile,
            ];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [
                $updatedAt,
                $logoImagePreview,
            ];
        }

        return [];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW , Action::DELETE, Action::INDEX);
    }

    public function index(AdminContext $context)
    {
        $settings = $this->entityManager->getRepository(NakaSiteSettings::class)->find(1);
        if (!$settings) {
            $settings = new NakaSiteSettings();
            $settings->setId(1);
            $this->entityManager->persist($settings);
            $this->entityManager->flush();
            $this->addFlash('warning', 'backend.flash.nakaSiteSettingsInitialized');
        }

        // Redirect to the detail or edit page as desired
        $url = $this->adminUrlGenerator
            ->setController(self::class)
            ->setAction('detail') // or 'edit' if you prefer
            ->setEntityId(1)
            ->generateUrl();

        return $this->redirect($url);
    }

}
