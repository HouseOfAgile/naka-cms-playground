<?php
namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Controller\Admin\AdminDashboardController;
use App\Entity\Menu;
use App\Entity\MenuItem;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use HouseOfAgile\NakaCMSBundle\Component\ContentManagement\NakaMenuManager;
use HouseOfAgile\NakaCMSBundle\Form\NakaMenuType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/menu')]
class MenuAdminController extends AbstractController
{
    #[Route(path: '/{menu}/configure', name: 'configure_menu', defaults: [
            EA::DASHBOARD_CONTROLLER_FQCN => AdminDashboardController::class,
        ])]
    public function configureMenu(
        Request $request,
        Menu $menu,
        NakaMenuManager $nakaMenuManager
    ): \Symfony\Component\HttpFoundation\Response {

        $form = $this->createForm(NakaMenuType::class, $menu, [
            'orderedMenuItemsArray' => $menu->getOrderedMenuItemsArray(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $newOrderString = $form->get("newOrder")->getData();
                $newOrder       = explode(',', $newOrderString);
                $result         = $nakaMenuManager->updateMenuItemPosition($menu, $newOrder);
                if ($result) {
                    $this->addFlash('success', sprintf('Menu \'%s\' has been configured', $menu));
                } else {
                    $this->addFlash('warning', sprintf('Menu \'%s\' cannot been configured', $menu));
                }

                return $this->redirectToRoute('admin_menu_index');
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
        return $this->redirectToRoute('admin_menu_index');
    }
}
