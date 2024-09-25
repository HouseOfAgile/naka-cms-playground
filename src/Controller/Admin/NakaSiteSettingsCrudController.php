<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\NakaSiteSettings;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
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

    public function configureFields(string $pageName): iterable
    {
        $logoImageFile = TextField::new ('logoImageFile')
            ->setHelp('backend.nakaSiteSettings.logoImageFile.help')
            ->setFormType(VichImageType::class)
            ->setLabel('Logo Image')
            ->onlyOnForms();

        $logoImage = ImageField::new ('logoImage.name')
            ->setBasePath('/%app.path.site_images%/')
            ->setLabel('Logo Image')
            ->onlyOnDetail();

        $logoTransparentImageFile = TextField::new ('logoTransparentImageFile')
            ->setFormType(VichImageType::class)
            ->setLabel('Transparent Logo Image')
            ->onlyOnForms();

        $logoTransparentImage = ImageField::new ('logoTransparentImage.name')
            ->setBasePath('/%app.path.site_images%/')
            ->setLabel('Transparent Logo Image')
            ->onlyOnDetail();

        $defaultBackgroundImageFile = TextField::new ('defaultBackgroundImageFile')
            ->setFormType(VichImageType::class)
            ->setLabel('Default Background Image')
            ->onlyOnForms();

        $defaultBackgroundImage = ImageField::new ('defaultBackgroundImage.name')
            ->setBasePath('/%app.path.site_images%/')
            ->setLabel('Default Background Image')
            ->onlyOnDetail();

        $updatedAt = DateTimeField::new ('updatedAt')
            ->setLabel('Last Updated')
            ->hideOnForm();

        return [
            $logoImageFile,
            $logoImage,
            $logoTransparentImageFile,
            $logoTransparentImage,
            $defaultBackgroundImageFile,
            $defaultBackgroundImage,
            $updatedAt,
        ];
    }

    public function index(AdminContext $context)
    {
        $url = $this->adminUrlGenerator
            ->setController(self::class)
            ->setAction('edit')
            ->setEntityId(1)
            ->generateUrl();

        return $this->redirect($url);
    }
}
