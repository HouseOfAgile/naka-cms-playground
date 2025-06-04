<?php

namespace HouseOfAgile\NakaCMSBundle\Command;

use HouseOfAgile\NakaCMSBundle\Service\DeepLTranslationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'naka:translate',
    description: 'Translate YAML files using DeepL for specified or all languages, with interactive update confirmations and statistics.',
)]
class TranslateCommand extends Command
{
    private DeepLTranslationService $deepLTranslationService;
    private string $translationsDir;
    private array $allLocales;
    private int $apiCallDelayMs;

    // Flag & counters
    private bool $skipExistingTranslations = false;
    private int $parsedFiles               = 0;
    private int $newKeysAdded              = 0;
    private int $existingKeysUpdated       = 0;
    private int $unchangedKeys             = 0;

    // Mapping country codes -> valid DeepL language codes
    private array $deepLSupportedLanguages = [
        'cz' => 'cs',
        'at' => 'de',
        'ch' => 'de',
        'be' => 'nl',
    ];

    public function __construct(
        DeepLTranslationService $deepLTranslationService,
        string $translationsDir,
        array $allLocales,
        int $apiCallDelayMs = 0
    ) {
        $this->deepLTranslationService = $deepLTranslationService;
        $this->translationsDir         = $translationsDir;
        $this->allLocales             = $allLocales;
        $this->apiCallDelayMs         = $apiCallDelayMs;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('sourceLang', InputArgument::REQUIRED, 'The source language code (e.g., en, fr).')
            ->addArgument('targetLang', InputArgument::OPTIONAL, 'The target language code. If omitted, translates to all configured locales except the source.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force update all translations without confirmation.')
            ->addOption('skip-existing', null, InputOption::VALUE_NONE, 'Skip updating existing translations.')
            ->addOption('domain', null, InputOption::VALUE_OPTIONAL, 'The translation domain to process (default: all).')
            ->addOption('delay', null, InputOption::VALUE_OPTIONAL, 'Delay between DeepL API calls in milliseconds.', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $sourceLang = strtolower($input->getArgument('sourceLang'));
        $targetLang = $input->getArgument('targetLang')
            ? [strtolower($input->getArgument('targetLang'))]
            : array_diff(array_map('strtolower', $this->allLocales), [$sourceLang]);

        $this->skipExistingTranslations = $input->getOption('skip-existing');
        $domain                         = $input->getOption('domain');
        $forceUpdate                    = $input->getOption('force');
        $this->apiCallDelayMs           = (int) $input->getOption('delay');

        // Validate delay
        if ($this->apiCallDelayMs < 0) {
            $io->error('Delay must be a non-negative integer.');
            return Command::FAILURE;
        }

        // Map & validate source language
        $deepLSourceLang = $this->deepLSupportedLanguages[$sourceLang] ?? $sourceLang;
        if (!in_array($deepLSourceLang, $this->getDeepLSupportedLanguages(), true)) {
            $io->error("Invalid source language: $deepLSourceLang (mapped from '$sourceLang'). Must be a valid DeepL-supported language.");
            return Command::FAILURE;
        }

        // Estimate total translations
        $totalTranslations = $this->estimateTotalTranslations($targetLang, $domain, $deepLSourceLang);
        $progressBar       = new ProgressBar($output, $totalTranslations);
        $this->initializeProgressBar($progressBar);
        $progressBar->start();

        // Process each target language
        foreach ($targetLang as $lang) {
            $deepLTargetLang = $this->deepLSupportedLanguages[$lang] ?? $lang;
            if (!in_array($deepLTargetLang, $this->getDeepLSupportedLanguages(), true)) {
                $io->warning("Skipping unsupported target language: $lang (mapped to '$deepLTargetLang').");
                continue;
            }

            $this->translateLanguage($deepLSourceLang, $deepLTargetLang, $io, $domain, $forceUpdate, $progressBar);
        }

        $progressBar->finish();
        $io->newLine(2);
        $io->success('Translation process completed.');
        $this->displayStatistics($io);

        return Command::SUCCESS;
    }

    /**
     * Summation of all keys found in source YAML files for each target language.
     */
    private function estimateTotalTranslations(array $targetLang, ?string $domain, string $sourceLang): int
    {
        $totalTranslations = 0;

        foreach ($targetLang as $lang) {
            $finder  = new Finder();
            $pattern = $domain
                ? "{$domain}.{$sourceLang}.*"
                : "*.$sourceLang.*";

            $finder->in($this->translationsDir)
                ->files()
                ->name($pattern);

            foreach ($finder as $file) {
                if (! preg_match('/\.(yml|yaml)$/i', $file->getFilename())) {
                    continue;
                }

                $sourceContent = Yaml::parseFile($file->getRealPath());
                $totalTranslations += is_array($sourceContent) ? count($sourceContent) : 0;
            }
        }

        return $totalTranslations;
    }

    private function initializeProgressBar(ProgressBar $progressBar): void
    {
        $progressBar->setFormat(
            " %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% " .
                "Memory: %memory:6s% | New: %newKeys%, Updated: %updatedKeys%, Skipped: %skippedKeys%"
        );
        $progressBar->setMessage('0', 'newKeys');
        $progressBar->setMessage('0', 'updatedKeys');
        $progressBar->setMessage('0', 'skippedKeys');
    }
    private function translateLanguage(
        string $sourceLang,
        string $targetLang,
        SymfonyStyle $io,
        ?string $domain,
        bool $forceUpdate,
        ProgressBar $progressBar
    ): void {
        $finder  = new Finder();
        $pattern = $domain
            ? "{$domain}.{$sourceLang}.*"
            : "*.$sourceLang.*";

        $finder->in($this->translationsDir)
            ->files()
            ->name($pattern);

        foreach ($finder as $file) {
            if (! preg_match('/\.(yml|yaml)$/i', $file->getFilename())) {
                continue;
            }

            $this->parsedFiles++;
            $sourceFile    = $file->getRealPath();
            $targetFile    = str_replace(".$sourceLang.", ".$targetLang.", $sourceFile);
            $sourceContent = Yaml::parseFile($sourceFile);
            $targetContent = file_exists($targetFile) ? Yaml::parseFile($targetFile) : [];

            if (! is_array($sourceContent)) {
                continue; // skip invalid or empty YAML
            }

            foreach ($sourceContent as $key => $value) {
                // Skip empty/null
                if (! is_string($value) || trim($value) === '') {
                    $io->note("Skipping empty or null key: $key");
                    $this->unchangedKeys++;
                    continue;
                }

                // Check if there's already a translation
                if (isset($targetContent[$key])) {
                    if (
                        $this->skipExistingTranslations
                        || (
                            ! $forceUpdate
                            && ! $io->confirm("Key '$key' has an existing translation. Update? Existing: '{$targetContent[$key]}'", false)
                        )
                    ) {
                        $this->unchangedKeys++;
                        continue;
                    }
                    $this->existingKeysUpdated++;
                } else {
                    $this->newKeysAdded++;
                }

                // 1. Extract placeholders like %something%
                $placeholderMap     = [];
                $textForTranslation = $this->extractPlaceholders($value, $placeholderMap);

                // 2. Translate the text with placeholders replaced
                $translatedValue = $this->deepLTranslationService->translate(
                    $textForTranslation,
                    $sourceLang,
                    $targetLang
                );

                // 3. Re-insert placeholders
                $finalTranslated = $this->restorePlaceholders($translatedValue, $placeholderMap);

                $targetContent[$key] = $finalTranslated;
                $this->updateProgressBar($progressBar);
            }

            file_put_contents($targetFile, Yaml::dump($targetContent, 4, 2));
        }
    }

    /**
     * Finds placeholders (e.g., %username%) in the text, replaces them with unique tokens,
     * and stores a map so we can re-insert them post-translation.
     */
    private function extractPlaceholders(string $text, array &$placeholderMap): string
    {
        // Match everything from % up to the next %
        // e.g., %username%, %count%, etc.
        $pattern = '/%([^%]+)%/';

        return preg_replace_callback($pattern, function ($matches) use (&$placeholderMap) {
            $placeholder = $matches[0]; // e.g. "%username%"
            $token       = 'PLACEHOLDER_' . count($placeholderMap);

            // store in a map
            $placeholderMap[$token] = $placeholder;

            return $token;
        }, $text);
    }

    /**
     * Takes the translated text with tokens like 'PLACEHOLDER_0'
     * and swaps them back to the original placeholders (e.g., %username%).
     */
    private function restorePlaceholders(string $translatedText, array $placeholderMap): string
    {
        foreach ($placeholderMap as $token => $originalPlaceholder) {
            $translatedText = str_replace($token, $originalPlaceholder, $translatedText);
        }
        return $translatedText;
    }

    private function updateProgressBar(ProgressBar $progressBar): void
    {
        $progressBar->setMessage((string) $this->newKeysAdded, 'newKeys');
        $progressBar->setMessage((string) $this->existingKeysUpdated, 'updatedKeys');
        $progressBar->setMessage((string) $this->unchangedKeys, 'skippedKeys');
        $progressBar->advance();
    }

    private function displayStatistics(SymfonyStyle $io): void
    {
        $io->section('Translation Statistics');
        $io->listing([
            "Parsed files: {$this->parsedFiles}",
            "New keys added: {$this->newKeysAdded}",
            "Existing keys updated: {$this->existingKeysUpdated}",
            "Unchanged keys: {$this->unchangedKeys}",
        ]);
    }

    /**
     * List of valid language codes that DeepL recognizes.
     */
    private function getDeepLSupportedLanguages(): array
    {
        return [
            'en',
            'de',
            'fr',
            'es',
            'it',
            'nl',
            'pl',
            'ru',
            'ja',
            'zh',
            'cs',
            'pt',
            'sv',
            'da',
            'fi',
            'hu',
        ];
    }
}
