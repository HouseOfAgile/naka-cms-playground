<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use HouseOfAgile\NakaCMSBundle\Utils\StringUtils;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class StringUtilsExtension extends AbstractExtension
{
    // public function getFunctions()
    // {
    //     return [
    //         new TwigFunction('smartTruncateString', [$this, 'smartTruncateString'], ['maxChar', 'breakWord', 'ellipsis']),
    //     ];
    // }
    /** @var StringUtils */
    private $stringUtils;

    public function __construct(StringUtils $stringUtils)
    {
        $this->stringUtils = $stringUtils;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('smartTruncateString', [$this, 'smartTruncateString']),
            new TwigFilter('toSnakeCase', [$this, 'toSnakeCase']),
            new TwigFilter('toCamelCase', [$this, 'toCamelCase']),
            new TwigFilter('toHumanWords', [$this, 'toHumanWords']),
            new TwigFilter('replaceCustom', [$this, 'replaceCustom']),
        ];
    }

    public  function smartTruncateString(
        $text,
        $maxChar = 64,
        $breakWord = false,
        $ellipsis = '...'
    ) {
        return $this->stringUtils->smartTruncateString($text, $maxChar, $breakWord, $ellipsis);
    }

    public  function toSnakeCase(string $value): string
    {
        return $this->stringUtils->smartTruncateString($value);
    }

    public function toCamelCase(string $value): string
    {
        return $this->stringUtils->toCamelCase($value);
    }

    /**
     * toHumanWords
     *
     * @see StringUtils::toHumanWords(string $value)
     */
    public function toHumanWords(string $value): string
    {
        return $this->stringUtils->toHumanWords($value);
    }

    public function replaceCustom(string $text, string $replacement = 'has_been_replaced'): string
    {
        $pattern = '/##(.*?)##/';
        return preg_replace($pattern, $replacement, $text);
    }
}
