<?php

namespace HouseOfAgile\NakaCMSBundle\Component\ContentManagement;

use App\Entity\BlockElement;
use App\Entity\BlockElementType;
use App\Entity\Page;
use App\Entity\PageBlockElement;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use HouseOfAgile\NakaCMSBundle\Component\ContentManagement\BlockManager;
use HouseOfAgile\NakaCMSBundle\Repository\BlockElementRepository;

class PageBlockManager
{

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var BlockManager */
    protected $blockManager;

    /** @var BlockElementRepository */
    protected $blockElementRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        BlockManager $blockManager,
        BlockElementRepository $blockElementRepository
    ) {
        $this->entityManager = $entityManager;
        $this->blockManager = $blockManager;
        $this->blockElementRepository = $blockElementRepository;
    }

    public function addBlockToPage(BlockElementType $blockElementType, $name, Page $page): BlockElement
    {
        $lastPosition =  $page->findPosition('max');
        $blockElement = $this->blockManager->createBlockElementFromBlockElementType($blockElementType, $name);
        $pageBlockElement = new PageBlockElement;
        $pageBlockElement->setBlockElement($blockElement);
        $pageBlockElement->setPosition($lastPosition + 1);
        $page->addPageBlockElement($pageBlockElement);
        $this->entityManager->persist($pageBlockElement);
        $this->entityManager->flush();
        return $blockElement;
    }

    public function renameSlugForAllBlocksOfPage(Page $page, string $renamedSlugKey = null)
    {
        foreach ($page->getPageBlockElements() as $pageBlockElement) {
            /** @var PageBlockElement $pageBlockElement  */
            $this->blockManager->renameSlugKey($pageBlockElement->getBlockElement()->getHtmlCode(), $renamedSlugKey);
            $this->blockManager->renameSlugKey($pageBlockElement->getBlockElement()->getJsCode(), $renamedSlugKey);
            $this->blockManager->renameSlugKey($pageBlockElement->getBlockElement()->getCssCode(), $renamedSlugKey);
        }
    }
}
