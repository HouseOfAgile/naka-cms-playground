<?php

namespace HouseOfAgile\NakaCMSBundle\Component\ContentManagement;

use App\Entity\Menu;
use App\Entity\MenuItem;
use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use HouseOfAgile\NakaCMSBundle\DBAL\Types\NakaMenuItemType;
use HouseOfAgile\NakaCMSBundle\Repository\MenuItemRepository;
use HouseOfAgile\NakaCMSBundle\Repository\MenuRepository;

class NakaMenuManager
{

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var MenuRepository */
    protected $menuRepository;

    /** @var MenuItemRepository */
    protected $menuItemRepository;


    public function __construct(
        EntityManagerInterface $entityManager,
        MenuRepository $menuRepository,
        MenuItemRepository $menuItemRepository
    ) {
        $this->entityManager = $entityManager;
        $this->menuRepository = $menuRepository;
        $this->menuItemRepository = $menuItemRepository;
    }

    /**
     * addPageToMenu function: add page to menu, return true if ok, or false is already in
     *
     * @param Page $page
     * @param Menu $menu
     * @return boolean
     */
    public function addPageToMenu(Page $page, Menu $menu): bool
    {
        $menuItem = $this->getOrCreateMenuItemForPage($page);
        if (!$menu->getMenuItems()->contains($menuItem)) {
            $menu->addMenuItem($menuItem);
            $this->entityManager->persist($menu);
            $this->entityManager->flush();
            return true;
        } else {
            return false;
        }
    }

    public function getOrCreateMenuItemForPage(Page $page)
    {
        $menuItem = $this->menuItemRepository->findOneBy(['page' => $page]);
        if (!$menuItem) {
            $menuItem = new MenuItem();
            $menuItem->setPage($page);
            $menuItem->setType(NakaMenuItemType::TYPE_PAGE);
            if ($page->getName()) {
                $menuItem->setName($page->getName());
            } else {
                $menuItem->setName($page->getTitle());
            };
            $this->entityManager->persist($menuItem);
            $this->entityManager->flush();
        }
        return $menuItem;
    }

    public function updateMenuItemPosition(Menu $menu, $newOrder)
    {
        $position = 1;
        foreach ($newOrder as $menuItemId) {
            $menuItem = $this->menuItemRepository->findOneBy(['id' => $menuItemId]);
            $menuItem->setPosition($position);
            $position++;
            $this->entityManager->persist($menuItem);
        }
        $this->entityManager->flush();
        return true;
    }
}
