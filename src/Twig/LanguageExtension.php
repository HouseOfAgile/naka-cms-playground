<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use HouseOfAgile\NakaCMSBundle\Component\NakaParameter\NakaParameterManager;
use Symfony\Component\Intl\Languages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class LanguageExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('englishClean', [$this, 'englishClean']),
            new TwigFilter('getAlpha2', [$this, 'getAlpha2']),
            new TwigFilter('getAlpha3', [$this, 'getAlpha3']),
        ];
    }

    public function englishClean($code)
    {
        return str_replace('en', 'gb', $code);
    }

    public function getAlpha2($alpha3Code)
    {
        return Languages::getAlpha2Code($alpha3Code);
    }

    public function getAlpha3($alpha2Code)
    {
        return Languages::getAlpha3Code($alpha2Code);
    }
}
