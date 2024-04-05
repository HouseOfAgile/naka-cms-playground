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
        $result = $this->translator->translateText($text, $sourceLang, $targetLang);
        return $result->text;
    }

    /**
     * Translates HTML content from the source language to the target language.
     * This method specifically indicates to the API that the content is HTML.
     */
    public function translateHtml(string $html, string $sourceLang, string $targetLang): string
    {
        $result = $this->translator->translateText($html, $sourceLang, $targetLang, [
            'tag_handling' => 'xml', // Tells DeepL to preserve HTML/XML tags
            // Specify 'ignore_tags' to protect parts of the text from translation
        ]);
        return $result->text;
    }
}
