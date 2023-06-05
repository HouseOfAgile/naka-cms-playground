<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use HouseOfAgile\NakaCMSBundle\Component\NakaParameter\NakaParameterManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class NakaParameterExtension extends AbstractExtension
{
    /** @var NakaParameterManager */
    protected $nakaParameterManager;

    protected $allNakaParameter;

    public function __construct(NakaParameterManager $nakaParameterManager)
    {
        $this->nakaParameterManager = $nakaParameterManager;
        $this->allNakaParameter = $nakaParameterManager->getAllParameter();
    }

    public function getFilters()
    {
        return [
            new TwigFilter('nakaParameter', [$this, 'nakaParameter']),
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
}
