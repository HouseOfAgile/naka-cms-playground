<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

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

    public function getFilters()
    {
        return [
            new TwigFilter('smartTruncateString', [$this, 'smartTruncateString']),
        ];
    }

    /**
     * Truncate text by characters
     *
     * @param $text String - text to truncate
     * @param $maxChar Integer - number of characters to truncate to - default 64
     * @param $breakWord Boolean - if true, will break on word boundaries - when false, could lead to strings longer than $maxChar
     * @param $ellipsis String - if set, will append to truncated text, '…' character by default
     */
    public static function smartTruncateString(
        $text,
        $maxChar = 64,
        $breakWord = false,
        $ellipsis = '...'
    ) {
        if (empty($text)) {
            return null;
        }

        if ($breakWord) {
            $truncate = substr($text, 0, $maxChar);
            return $ellipsis && strlen($truncate) < strlen($text)
                ? $truncate . $ellipsis
                : $truncate;
        }

        // This will allow strings longer than $maxChar
        // eg. if the LAST WORD is within the first $maxChar maxChar
        if (strlen($text) > $maxChar) {
            $shortened = (substr($text, 0, strpos($text, ' ', $maxChar)));
            // make sure we don't cut off mid last-word and wind up with nothing
            $final = $ellipsis && strlen($shortened) > 0
                ? $shortened . $ellipsis
                : $text;
        } else {
            $final = $text;
        }
        return $final;
    }
}
