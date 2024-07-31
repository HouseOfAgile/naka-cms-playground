<?php

namespace HouseOfAgile\NakaCMSBundle\Component\NakaAdManagement;

use App\Entity\AdBanner;
use App\Repository\AdBannerRepository;
use App\Repository\AdNetworkRepository;

class NakaAdService
{
    private $adNetworkRepository;
    private $adBannerRepository;

    public function __construct(AdNetworkRepository $adNetworkRepository, AdBannerRepository $adBannerRepository)
    {
        $this->adNetworkRepository = $adNetworkRepository;
        $this->adBannerRepository = $adBannerRepository;
    }

    public function getBanner($networkName, $bannerName): AdBanner
    {
        $network = $this->adNetworkRepository->findOneBy(['name' => $networkName]);
        if (!$network) {
            throw new \InvalidArgumentException("Network $networkName not found.");
        }

        $banner = $this->adBannerRepository->findOneBy(['name' => $bannerName, 'network' => $network]);
        if (!$banner) {
            throw new \InvalidArgumentException("Banner $bannerName for network $networkName not found.");
        }

        return $banner;
    }

    public function getScripts($networkName = null)
    {
        if ($networkName) {
            $network = $this->adNetworkRepository->findOneBy(['name' => $networkName]);
            return $network ? $network->getScripts() : '';
        }

        $networks = $this->adNetworkRepository->findAll();
        $scripts = '';
        foreach ($networks as $network) {
            $scripts .= $network->getScripts() . "\n";
        }
        return $scripts;
    }
}
