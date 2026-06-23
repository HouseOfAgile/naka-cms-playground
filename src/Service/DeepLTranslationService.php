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
     * Translate several texts to the same target language.
     *
     * Texts are grouped by whether they contain HTML so that DeepL's
     * `tag_handling=xml` option (which would mangle plain text) is only applied
     * to HTML fields. This keeps the whole batch to at most two API requests per
     * target language instead of one request per field.
     *
     * @param array<string, string> $texts Keyed list of source texts.
     * @return array<string, string> Translations keyed exactly like $texts (original order preserved).
     */
    public function translateBatch(array $texts, string $sourceLang, string $targetLang): array
    {
        if ($texts === []) {
            return [];
        }

        if ($this->apiCallDelayMs > 0) {
            usleep($this->apiCallDelayMs * 1000);
        }

        $targetLang = $this->updateLanguageCode($targetLang);

        $htmlKeys = [];
        $plainKeys = [];
        foreach ($texts as $key => $text) {
            if ($this->containsHtml((string) $text)) {
                $htmlKeys[] = $key;
            } else {
                $plainKeys[] = $key;
            }
        }

        $results = [];
        foreach ([[$plainKeys, false], [$htmlKeys, true]] as [$group, $isHtml]) {
            if ($group === []) {
                continue;
            }
            $payload = array_map(static fn ($key) => (string) $texts[$key], $group);
            $options = [TranslateTextOptions::FORMALITY => $this->formality];
            if ($isHtml) {
                $options[TranslateTextOptions::TAG_HANDLING] = 'xml';
            }
            $translated = $this->translator->translateText($payload, $sourceLang, $targetLang, $options);
            foreach ($group as $index => $key) {
                $results[$key] = $translated[$index]->text;
            }
        }

        // Restore the original key order.
        $ordered = [];
        foreach (array_keys($texts) as $key) {
            $ordered[$key] = $results[$key] ?? '';
        }

        return $ordered;
    }

    /**
     * Current DeepL character usage for the account, used to keep within the
     * free-tier monthly budget.
     *
     * @return array{count: int|null, limit: int|null}
     */
    public function getCharacterUsage(): array
    {
        $usage = $this->translator->getUsage();

        return [
            'count' => $usage->character?->count,
            'limit' => $usage->character?->limit,
        ];
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