<?php

namespace HouseOfAgile\NakaCMSBundle\Component\ContentManagement;

use App\Entity\BlockElement;
use App\Entity\Menu;
use App\Entity\MenuItem;
use App\Entity\Page;
use App\Entity\PageBlockElement;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use HouseOfAgile\NakaCMSBundle\DBAL\Types\NakaMenuItemType;
use HouseOfAgile\NakaCMSBundle\Repository\MenuItemRepository;
use HouseOfAgile\NakaCMSBundle\Repository\MenuRepository;
use HouseOfAgile\NakaCMSBundle\Repository\PageBlockElementRepository;

class PageBlockManager
{

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var PageBlockElementRepository */
    protected $pageBlockElementRepository;



    public function __construct(
        EntityManagerInterface $entityManager,
        PageBlockElementRepository $pageBlockElementRepository
    ) {
        $this->entityManager = $entityManager;
        $this->pageBlockElementRepository = $pageBlockElementRepository;
    }


    public function addBlockToPage(BlockElement $blockElement, Page $page): bool
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
}
