<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use Symfony\Component\Intl\Languages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class LanguageExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('englishClean', [$this, 'englishClean']),
            new TwigFilter('getLanguage', [$this, 'getLanguage']),
            new TwigFilter('getAlpha2', [$this, 'getAlpha2']),
            new TwigFilter('getAlpha3', [$this, 'getAlpha3']),
        ];
    }

    public function englishClean($code)
    {
        return str_replace('en', 'gb', strtolower($code));
    }

    public function getLanguage($code, $displayedLocale = null)
    {
        $code = strtolower($code);

        // Rewrite specific country codes to appropriate language codes
        if ($code === 'us') {
            $code = 'en';
        }

        // Avoid crashing if the code is invalid or unknown
        if (!Languages::exists($code)) {
            return strtoupper($code); // fallback display (e.g. "US")
        }

        return Languages::getName($code, $displayedLocale);
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
