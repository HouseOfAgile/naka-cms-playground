<?php

namespace HouseOfAgile\NakaCMSBundle\Component\WebsiteInfo;

use App\Repository\WebsiteInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class WebsiteInfoService
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var WebsiteInfoRepository */
    protected $websiteInfoRepository;

    /** @var WebsiteInfo */
    protected $websiteInfo;

    /** @var RequestStack */
    protected $requestStack;

    public function __construct(
        EntityManagerInterface $entityManager,
        WebsiteInfoRepository $websiteInfoRepository,
        RequestStack $requestStack
    ) {
        $this->entityManager = $entityManager;
        $this->websiteInfoRepository = $websiteInfoRepository;
        $this->requestStack = $requestStack;
        $this->loadMainWebsiteInfo();
    }

    public function isMainWebsiteInfoSet()
    {
        return $this->websiteInfo != null;
    }

    public function loadMainWebsiteInfo()
    {
        if ($this->websiteInfo == null) {
            $this->websiteInfo =  $this->websiteInfoRepository->find(1);
        }
    }

    public function getWebsiteInfo()
    {
        return $this->websiteInfo;
    }

    public function getTranslatedTitle()
    {
        return $this->isMainWebsiteInfoSet() ? $this->websiteInfo->translate($this->requestStack->getSession()->get('_locale'))->getTitle() : '';
    }

    public function getTranslatedCatchPhrase()
    {
        return $this->isMainWebsiteInfoSet() ? $this->websiteInfo->translate($this->requestStack->getSession()->get('_locale'))->getCatchPhrase() : '';
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
