<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use App\Entity\AdNetwork;
use HouseOfAgile\NakaCMSBundle\Component\NakaAdManagement\NakaAdService;
use HouseOfAgile\NakaCMSBundle\Config\AdNetworkType;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NakaAdExtension extends AbstractExtension
{
    private $adService;
    private $twig;
    private $adModeNone;
    private $adModeDummy;

    public function __construct(NakaAdService $adService, Environment $twig, $adModeNone, $adModeDummy)
    {
        $this->adService = $adService;
        $this->twig = $twig;
        $this->adModeNone = $adModeNone;
        $this->adModeDummy = $adModeDummy;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('naka_ad_banner', [$this, 'renderBanner'], ['is_safe' => ['html']]),
            new TwigFunction('naka_ad_scripts', [$this, 'renderScripts'], ['is_safe' => ['html']]),
        ];
    }

    public function renderBanner($network, $bannerName)
    {
        $banner = $this->adService->getBanner($network, $bannerName);
        if ($this->adModeNone) {
            return $this->twig->render('@NakaCMS/ad_management/ad_mode_none.html.twig', [
                'network' => $network,
                'bannerName' => $bannerName,
                'banner' => $banner,
                'client' => $banner->getNetwork()->getClient(),
            ]);
        } elseif ($this->adModeDummy) {
            return $this->twig->render('@NakaCMS/ad_management/ad_mode_dummy.html.twig', [
                'network' => $network,
                'bannerName' => $bannerName,
                'banner' => $banner,
                'client' => $banner->getNetwork()->getClient(),
            ]);
        }
        switch ($banner->getNetwork()->getType()) {
            case AdNetworkType::GOOGLE_ADSENSE:
                return $this->twig->render('@NakaCMS/ad_management/banner_adsense.html.twig', [
                    'banner' => $banner,
                    'client' => $banner->getNetwork()->getClient(),
                ]);
                break;

            default:
                # code...
                break;
        }
    }

    public function renderScripts($network = null)
    {
        return $this->twig->render('@NakaCMS/ad_management/scripts.html.twig', [
            'scripts' => $this->adService->getScripts($network),
        ]);
    }
}
