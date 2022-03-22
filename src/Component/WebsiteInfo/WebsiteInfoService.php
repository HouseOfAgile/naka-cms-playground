<?php

namespace HouseOfAgile\NakaCMSBundle\Component\WebsiteInfo;

use App\Repository\WebsiteInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class WebsiteInfoService
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var WebsiteInfoRepository */
    protected $websiteInfoRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        WebsiteInfoRepository $websiteInfoRepository
    ) {
        $this->entityManager = $entityManager;
        $this->websiteInfoRepository = $websiteInfoRepository;
    }

    public function getWebsiteInfo()
    {
        return $this->websiteInfoRepository->find(1);
    }

    public function setOpeningHours($openingHours)
    {
        $websiteInfo = $this->getWebsiteInfo();
        if ($websiteInfo) {
            $websiteInfo->setOpeningHours($openingHours);
            $this->entityManager->persist($websiteInfo);
            $this->entityManager->flush();
        }
    }

    public function isOpeningHoursSet()
    {
        return $this->getWebsiteInfo() &&
            $this->getWebsiteInfo() &&
            $this->getWebsiteInfo()->getOpeningHours() != null;
    }
}
