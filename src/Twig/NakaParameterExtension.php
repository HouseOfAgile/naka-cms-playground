<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use HouseOfAgile\NakaCMSBundle\Component\NakaParameter\NakaParameterManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class NakaParameterExtension extends AbstractExtension
{
    /** @var NakaParameterManager */
    protected $nakaParameterManager;

    public function __construct(NakaParameterManager $nakaParameterManager)
    {
        $this->nakaParameterManager = $nakaParameterManager;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('nakaParameter', [$this, 'nakaParameter']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('existingNakaSocialParameters', [$this, 'existingNakaSocialParameters']),
        ];
    }

    public function nakaParameter($parameterKey)
    {
        $nakaParameter = $this->nakaParameterManager->getNakaParameter($parameterKey);
        if ($nakaParameter) {
            return $nakaParameter->getValue();
        } else {
            return null;
            // dd($this->allNakaParameter);
        }
    }
    public function existingNakaSocialParameters()
    {
        return $this->nakaParameterManager->getAllNakaSocialParameters();
    }
}
