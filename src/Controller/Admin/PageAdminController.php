<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Controller\Admin\AdminDashboardController;
use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use HouseOfAgile\NakaCMSBundle\Component\ContentManagement\NakaMenuManager;
use HouseOfAgile\NakaCMSBundle\Component\ContentManagement\PageBlockManager;
use HouseOfAgile\NakaCMSBundle\Form\ChooseBlockElementTypeType;
use HouseOfAgile\NakaCMSBundle\Form\ChooseMenuForPageType;
use HouseOfAgile\NakaCMSBundle\Repository\MenuRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/page")
 */
class PageAdminController extends AdminDashboardController
{
    /**
     * @Route("/{page}/add-page-to-menu", name="add_page_to_menu")
     */
    public function addPageToMenuAction(
        Request $request,
        Page $page,
        NakaMenuManager $nakaMenuManager
    ): \Symfony\Component\HttpFoundation\Response {

        $form = $this->createForm(ChooseMenuForPageType::class, null, [
            'add_submit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                $menu = $data['menu'];
                $result = $nakaMenuManager->addPageToMenu($page, $menu);
                if ($result) {
                    $this->addFlash('success', sprintf('Page \'%s\' has been added to Menu \'%s\'', $page, $menu));
                } else {
                    $this->addFlash('warning', sprintf('Page \'%s\' is already in Menu \'%s\'', $page, $menu));
                }

                return $this->redirect($this->adminUrlGenerator
                    ->setController(PageCrudController::class)
                    ->setAction(Action::INDEX)
                    ->generateUrl());
            } else {
                $this->addFlash('error', sprintf('Cannot add page \'%s\', check logs', $page));
            }
        }
        $viewParams = [
            'form' => $form->createView(),
            'page' => $page,
        ];

        return $this->render('@NakaCMS/backend/page/add_page_to_menu.html.twig', $viewParams);
    }
    /**
     * @Route("/{page}/add-block-to-page", name="add_block_to_page")
     */
    public function addBlockToPageAction(
        Request $request,
        Page $page,
        PageBlockManager $pageBlockManager
    ): \Symfony\Component\HttpFoundation\Response {

        $form = $this->createForm(ChooseBlockElementTypeType::class, null, [
            'add_submit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();

                $blockElement = $pageBlockManager->addBlockToPage(
                    $data['blockElementType'],
                    $data['name'],
                    $page
                );
                if ($blockElement) {
                    $this->addFlash('success', sprintf('Block \'%s\' has been added to Page \'%s\'', $blockElement, $page));
                } else {
                    $this->addFlash('warning', sprintf('Block \'%s\' cannot be added to \'%s\'', $page));
                }

                return $this->redirect($this->adminUrlGenerator
                    ->setController(PageCrudController::class)
                    ->setAction(Action::INDEX)
                    ->generateUrl());
            } else {
                $this->addFlash('error', sprintf('Cannot add page \'%s\', check logs', $page));
            }
        }
        $viewParams = [
            'form' => $form->createView(),
            'page' => $page,
        ];

        return $this->render('@NakaCMS/backend/page/choose_block_to_add.html.twig', $viewParams);
    }
}
