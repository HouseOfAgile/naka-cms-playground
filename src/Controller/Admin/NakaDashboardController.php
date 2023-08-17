<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use HouseOfAgile\NakaCMSBundle\Entity\BaseUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/{_locale}')]
class NakaDashboardController extends AbstractDashboardController
{
    /** @var AdminUrlGenerator */
    protected $adminUrlGenerator;
    protected $applicationName;

    public function __construct(
        AdminUrlGenerator $adminUrlGenerator,
        $applicationName
    ) {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->applicationName = $applicationName;
    }

    #[Route(path: '/admin')]
    public function indexNonLocalized(): RedirectResponse
    {
        // return $this->redirectToRoute('naka_localized_admin');
        return $this->redirectToRoute('admin_dashboard');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle(sprintf('%s Admin', $this->applicationName))
            ->setLocales(['en', 'de']);
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->addFormTheme('@NakaCMS/admin/a2lix_bootstrap_5_layout.html.twig')
            ->addFormTheme('@EasyAdmin/crud/form_theme.html.twig')
            ->addFormTheme('@NakaCMS/admin/crud/form_theme.html.twig')
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
            ->overrideTemplates([
                'crud/index' => '@NakaCMS/admin/crud/index.html.twig',
            ])
            // ->showEntityActionsInlined(true)
            ->setDefaultSort(['id' => 'DESC'])
            ->showEntityActionsInlined(true)

            ->setPaginatorPageSize(15);
    }

    public function configureActions(): Actions
    {
        $actions = parent::configureActions();
        // Remove New action by default for all Cruds
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $action) => $action->addCssClass('btn btn-danger text-white'))
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $action) => $action->addCssClass('btn btn-warning text-white'))
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn (Action $action) => $action->addCssClass('btn btn-info text-white'))
            // ->update(Crud::PAGE_INDEX, Action::BATCH_DELETE, fn (Action $action) => $action->addCssClass('text-white'))
            // We remove new by default and add it when needed on CRUD level
            // ->remove(Crud::PAGE_INDEX, Action::NEW)
        ;
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            // ->addJsFile('https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js')
            // ->addJsFile('https://code.jquery.com/jquery-3.6.0.js')
            // ->addJsFile('https://code.jquery.com/ui/1.12.1/jquery-ui.min.js')
            // ->addJsFile('js/somejs.js')
            // ->addCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome-animation/0.3.0/font-awesome-animation.min.css')
            ->addCssFile('css/admin.css');
    }
}
