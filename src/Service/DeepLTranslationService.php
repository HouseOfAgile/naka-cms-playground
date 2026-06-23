<?php

namespace HouseOfAgile\NakaCMSBundle\Service;

use DeepL\TranslateTextOptions;
use DeepL\Translator;

class DeepLTranslationService
{
    private Translator $translator;
    private int $apiCallDelayMs;
    private string $formality;

    /**
     * @param string $formality DeepL formality preference. Defaults to
     *                          'prefer_less' (informal) because GLS content is
     *                          written informally; 'prefer_*' variants fall back
     *                          to the default tone for target languages that do
     *                          not support formality, so they never error.
     */
    public function __construct(string $authKey, int $apiCallDelayMs = 0, string $formality = 'prefer_less')
    {
        $this->translator = new Translator($authKey);
        $this->apiCallDelayMs = $apiCallDelayMs;
        $this->formality = $formality;
    }

    public function translate(string $text, string $sourceLang, string $targetLang): string
    {
        // Apply delay if configured
        if ($this->apiCallDelayMs > 0) {
            usleep($this->apiCallDelayMs * 1000); // Convert ms to microseconds
        }

        $targetLang = $this->updateLanguageCode($targetLang);
        $options = [
            TranslateTextOptions::FORMALITY => $this->formality,
        ];
        if ($this->containsHtml($text)) {
            $options[TranslateTextOptions::TAG_HANDLING] = 'xml'; // Tells DeepL to preserve HTML/XML tags
        }
        $result = $this->translator->translateText($text, $sourceLang, $targetLang, $options);

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

    private function updateLanguageCode($langCode)
    {
        $defaultRegions = [
            'en' => 'US',
        ];

        if (strlen($langCode) == 2) {
            if (array_key_exists($langCode, $defaultRegions)) {
                return $langCode . '-' . $defaultRegions[$langCode];
            }
        }

        return $langCode;
    }
}