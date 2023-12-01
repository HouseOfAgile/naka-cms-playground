<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigColorExtension extends AbstractExtension
{
    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    public function getFunctions()
    {
        return [
            new TwigFunction('getRandomHexColor', [$this, 'getRandomHexColor']),
        ];
    }

    public function getRandomHexColor($limit = 150)
    {
        $hc = '';
        for ($n = 1; $n <= 3; $n++) {
            $hc .= str_pad(dechex(mt_rand(0, $limit)), 2, '0', STR_PAD_LEFT);
        }
        return '#' . $hc;
    }
}
