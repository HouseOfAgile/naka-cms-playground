<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigColorExtension extends AbstractExtension
{
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
