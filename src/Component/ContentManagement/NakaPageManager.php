<?php

namespace HouseOfAgile\NakaCMSBundle\Component\ContentManagement;

use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use HouseOfAgile\NakaCMSBundle\Repository\PageBlockElementRepository;

class NakaPageManager
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

    public function updatePageBlockElementsPosition(Page $page, $newOrder)
    {
        $position = 1;
        foreach ($newOrder as $pageBlockElementId) {
            $pageBlockElement = $this->pageBlockElementRepository->findOneBy(['id' => $pageBlockElementId]);
            $pageBlockElement->setPosition($position);
            $position++;
            $this->entityManager->persist($pageBlockElement);
        }
        $this->entityManager->flush();
        return true;
    }
}
