<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use HouseOfAgile\NakaCMSBundle\Component\OpeningHours\OpeningHoursManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OpeningHoursExtension extends AbstractExtension
{
    /** @var OpeningHoursManager */
    private $openingHoursManager;

    public function __construct(OpeningHoursManager $openingHoursManager)
    {
        $this->openingHoursManager = $openingHoursManager;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('openingStatus', [$this, 'openingStatus']),
        ];
    }

    public function openingStatus()
    {
        return $this->openingHoursManager->getOpeningStatus();
    }
}
