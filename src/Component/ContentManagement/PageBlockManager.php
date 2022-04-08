<?php

namespace HouseOfAgile\NakaCMSBundle\Component\ContentManagement;

use App\Entity\BlockElement;
use App\Entity\BlockElementType;
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

    public function createBlockElementFromBlockElementType(BlockElementType $blockElementType, $name): BlockElement
    {
        $newBlockElement = new BlockElement;
        $newBlockElement->setName($name);
        $newBlockElement->copyFromBlockElementType($blockElementType);
        $this->entityManager->persist($newBlockElement);
        $this->entityManager->flush();
        return $newBlockElement;
    }

    public function addBlockToPage(BlockElementType $blockElementType, $name, Page $page): BlockElement
    {
        $lastPosition =  $page->findPosition('max');
        $blockElement = $this->createBlockElementFromBlockElementType($blockElementType, $name);
        $pageBlockElement = new PageBlockElement;
        $pageBlockElement->setBlockElement($blockElement);
        $pageBlockElement->setPosition($lastPosition);
        $page->addPageBlockElement($pageBlockElement);
        $this->entityManager->persist($pageBlockElement);
        $this->entityManager->flush();
        return $blockElement;
    }
}
