<?php

namespace HouseOfAgile\NakaCMSBundle\Utils;

class StringUtils
{
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

    /**
     * Transforms the given string into snake case
     * (e.g. `BankMethodName` -> `bank_method_name`).
     */
    public static function toSnakeCase(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/[^a-zA-Z0-9_]/', '_', $value);
        $value = preg_replace('/(?<=\\w)([A-Z])/', '_$1', $value);
        $value = preg_replace('/_{2,}/', '_', $value);
        $value = strtolower($value);

        return $value;
    }

    /**
     * Transforms the given string to camel case
     * (e.g. `Bank Method name` -> `BankMethodName`).
     */
    public static function toCamelCase(string $value): string
    {
        return strtr(ucwords(strtr($value, ['_' => ' ', '.' => ' ', '\\' => ' '])), [' ' => '']);
    }

    /**
     * Transforms the given string to camel case
     * (e.g. `BankMethodname` -> `Bank method name`).
     *
     * @param string $value
     * @return string
     */
    public static function toHumanWords(string $value): string
    {
        return str_replace('  ', ' ', ucfirst(trim(implode(' ', preg_split('/(?=[A-Z])/', $value)))));
    }
}
