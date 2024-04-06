<?php

namespace HouseOfAgile\NakaCMSBundle\Service;

use DeepL\Translator;

class DeepLTranslationService
{
    private Translator $translator;

    public function __construct(string $authKey)
    {
        $this->translator = new Translator($authKey);
    }

    public function translate(string $text, string $sourceLang, string $targetLang): string
    {
        $targetLang = $this->updateLanguageCode($targetLang);
        if ($this->containsHtml($text)) {
            $result = $this->translator->translateText($text, $sourceLang, $targetLang, [
                'tag_handling' => 'xml', // Tells DeepL to preserve HTML/XML tags
                // Specify 'ignore_tags' to protect parts of the text from translation
            ]);
        } else {
            $result = $this->translator->translateText($text, $sourceLang, $targetLang);
        }
        return $result->text;
    }

    /**
     * Checks if a string contains HTML tags.
     *
     * @param string $string The string to check.
     * @return bool True if the string contains HTML tags; otherwise, false.
     */
    private function containsHtml(string $string): bool
    {
        return $string !== strip_tags($string);
    }

    private function updateLanguageCode($langCode) {
        $defaultRegions = [
            'en' => 'US',
        ];
    
        if (strlen($langCode) == 2) {
            if (array_key_exists($langCode, $defaultRegions)) {
                return $langCode . '_' . $defaultRegions[$langCode];
            }
        }
    
        return $langCode;
    }
}
