<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\WebsiteInfo;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use HouseOfAgile\NakaCMSBundle\Admin\Field\TranslationField;
use HouseOfAgile\NakaCMSBundle\Form\OpeningHoursType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\RedirectResponse;

class WebsiteInfoCrudController extends AbstractCrudController
{
    /** @var AdminUrlGenerator */
    private $adminUrlGenerator;

    public function __construct(
        AdminUrlGenerator $adminUrlGenerator
    ) {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return WebsiteInfo::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsAsDropdown(false)
            ->overrideTemplate('crud/detail', '@NakaCMS/backend/website-info/show-website-info.html.twig');
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        $fieldsConfig = [
            'title' => [
                'field_type' => TextareaType::class,
                'required' => true,
                'label' => 'Title',
            ],
        ];

        $id = IdField::new('id');
        $translations = TranslationField::new('translations', 'Translations', $fieldsConfig)
            ->setRequired(false)
            ->hideOnIndex();

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$translations];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$translations];
        }
    }

    /**
     * getRedirectResponseAfterSave: override to redirect to specific route 
     *
     * @param AdminContext $context
     * @param string $action
     * @return RedirectResponse
     */
    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        $submitButtonName = $context->getRequest()->request->all()['ea']['newForm']['btn'];
        if ('saveAndReturn' === $submitButtonName) {
            return $this->redirectToRoute('website_info_dashboard');
        }

        return parent::getRedirectResponseAfterSave($context, $action);
    }
}
