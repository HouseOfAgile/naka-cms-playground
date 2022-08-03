<?php

namespace HouseOfAgile\NakaCMSBundle;

class HoaIpsum
{
    private $allLocales;

    private $supportedLocales;

    public function __construct(array $allLocales, array $supportedLocales)
    {
        $this->allLocales = $allLocales;
        $this->supportedLocales = $supportedLocales;
    }
}
