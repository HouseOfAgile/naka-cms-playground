<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Controller\Admin\AdminDashboardController;
use App\Entity\Menu;
use App\Entity\MenuItem;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use HouseOfAgile\NakaCMSBundle\Component\ContentManagement\NakaMenuManager;
use HouseOfAgile\NakaCMSBundle\Controller\Admin\MenuCrudController;
use HouseOfAgile\NakaCMSBundle\Controller\Admin\MenuItemCrudController;
use HouseOfAgile\NakaCMSBundle\Form\NakaMenuType;
use HouseOfAgile\NakaCMSBundle\Repository\MenuRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/menu')]
class MenuAdminController extends AdminDashboardController
{
    #[Route(path: '/{menu}/configure', name: 'configure_menu')]
    public function configureMenu(
        Request $request,
        Menu $menu,
        NakaMenuManager $nakaMenuManager
    ): \Symfony\Component\HttpFoundation\Response {

        $form = $this->createForm(NakaMenuType::class, $menu, [
            // 'items' => ,
            'orderedMenuItemsArray' => $menu->getOrderedMenuItemsArray()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $newOrderString = $form->get("newOrder")->getData();
                $newOrder = explode(',', $newOrderString);
                $result =$nakaMenuManager->updateMenuItemPosition($menu, $newOrder);
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

        return $this->render('@NakaCMS/backend/topic/menu/configure_menu.html.twig', $viewParams);
    }

    #[Route(path: '/menu-item/{menuItem}/duplicate', name: 'duplicate_menu_item')]
    public function duplicateMenuItem(
        Request $request,
        MenuItem $menuItem,
        EntityManagerInterface $entityManager
    ): \Symfony\Component\HttpFoundation\Response {
        if ($menuItem instanceof MenuItem) {
            $newMenuItem = clone $menuItem;
            $entityManager->persist($newMenuItem);
            $entityManager->flush();

            $this->addFlash('success', sprintf('MenuItem \'%s\' has been duplicated into \'%s\'', $menuItem, $newMenuItem));
        } else {
            $this->addFlash('danger', sprintf('Cannot duplicate MenuItem \'%s\', check logs', $menuItem));
        }
        return $this->redirect($this->adminUrlGenerator
            ->setController(MenuItemCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl());
    }
}
