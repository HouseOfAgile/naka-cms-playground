<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\Staff;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use HouseOfAgile\NakaCMSBundle\Admin\Field\TranslationField;
use HouseOfAgile\NakaCMSBundle\Form\BusinessRoleType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class StaffCrudController extends AbstractCrudController implements EventSubscriberInterface
{
    public static function getEntityFqcn(): string
    {
        return Staff::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fieldsConfig = [
            'staffRole' => [
                'field_type' => TextareaType::class,
                'required' => true,
                'label' => 'Staff Role',
            ],
            'presentation' => [
                'field_type' => CKEditorType::class,
                'required' => true,
                'label' => 'Presentation',
            ],
        ];

        $id = IdField::new('id');
        $slug = TextField::new('slug')->hideOnForm();

        $firstName = TextField::new('firstName');
        $lastName = TextField::new('lastName');
        $doctolibUrl = TextField::new('doctolibUrl');
        $staffPicture = AssociationField::new('staffPicture');
        $staffSettingsPanel = FormField::addPanel('backend.form.staff.staffSettingsPanel');
        $businessRole = TextField::new('businessRole')->setFormType(BusinessRoleType::class);

        $staffDetailsPanel = FormField::addPanel('backend.form.staff.staffDetailsPanel');
        $staffTranslationsPanel = FormField::addPanel('backend.form.staff.staffTranslationsPanel')
            ->setHelp('backend.form.staff.staffTranslationsPanel.help');
        $translations = TranslationField::new('translations', 'Translations', $fieldsConfig)
            ->setRequired(false)
            ->hideOnIndex();

        $isActive = BooleanField::new('isActive')->setLabel('is this staff active')
            ->setPermission('ROLE_SUPER_ADMIN');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $firstName, $lastName, $slug, $businessRole, $isActive];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $firstName, $lastName, $slug, $businessRole, $isActive];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$staffSettingsPanel, $businessRole, $staffDetailsPanel, $firstName, $lastName, $doctolibUrl, $staffTranslationsPanel, $translations, $isActive];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$staffSettingsPanel, $businessRole, $staffDetailsPanel, $firstName, $lastName, $doctolibUrl, $staffPicture , $staffTranslationsPanel, $translations, $slug, $isActive];
        }
    }
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => 'generateSlug',
            BeforeEntityUpdatedEvent::class => 'generateSlug',
        ];
    }

    /** @internal */
    public function generateSlug($event)
    {
        /** @var Staff $staff */
        $staff = $event->getEntityInstance();
        if (!($staff instanceof Staff)) {
            return;
        }
        $staff->generateSlug();
    }
}
