<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Controller\Admin\AdminDashboardController;
use App\Entity\Menu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use HouseOfAgile\NakaCMSBundle\Component\ContentManagement\NakaMenuManager;
use HouseOfAgile\NakaCMSBundle\Controller\Admin\MenuCrudController;
use HouseOfAgile\NakaCMSBundle\Form\NakaPositionType;
use HouseOfAgile\NakaCMSBundle\Repository\MenuRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/menu")
 */
class MenuAdminController extends AdminDashboardController
{
    /**
     * @Route("/{menu}/configure", name="configure_menu")
     */
    public function configureMenu(
        Request $request,
        Menu $menu,
        NakaMenuManager $nakaMenuManager
    ): \Symfony\Component\HttpFoundation\Response {

        $form = $this->createForm(NakaPositionType::class, null, [
            'items' => $menu->getMenuItems(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                dd($data);
                $result = false;
                if ($result) {
                    $this->addFlash('success', sprintf('Menu \'%s\' has been configured', $menu));
                } else {
                    $this->addFlash('warning', sprintf('Menu \'%s\' cannot been configured', $menu));
                }

                return $this->redirect($this->adminUrlGenerator
                    ->setController(MenuCrudController::class)
                    ->setAction(Action::INDEX)
                    ->generateUrl());
            }
        }
        $viewParams = [
            'form' => $form->createView(),
            'menu' => $menu,
        ];

        return $this->render('@NakaCMS/backend/menu/configure_menu.html.twig', $viewParams);
    }
}
