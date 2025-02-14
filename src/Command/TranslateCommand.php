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
    private bool $skipExistingTranslations = false;
    private int $parsedFiles               = 0;
    private int $newKeysAdded              = 0;
    private int $existingKeysUpdated       = 0;
    private int $unchangedKeys             = 0;

    // DeepL-supported language mapping
    private array $deepLSupportedLanguages = [
        'cz' => 'cs', // Czech correction
        'at' => 'de', // Austria uses German
        'ch' => 'de', // Swiss German
        'be' => 'nl', // Belgium could be Dutch/French/German; default to Dutch here
    ];

    public function __construct(
        DeepLTranslationService $deepLTranslationService,
        string $translationsDir,
        array $allLocales
    ) {
        parent::__construct();
        $this->deepLTranslationService = $deepLTranslationService;
        $this->translationsDir         = $translationsDir;
        $this->allLocales              = $allLocales;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('sourceLang', InputArgument::REQUIRED, 'The source language code (e.g., en, fr, de).')
            ->addArgument('targetLang', InputArgument::OPTIONAL, 'The target language code. If omitted, translates to all configured locales.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force update all translations without confirmation.')
            ->addOption('skip-existing', null, InputOption::VALUE_NONE, 'Skip updating existing translations.')
            ->addOption('domain', null, InputOption::VALUE_OPTIONAL, 'The translation domain to process (default: all).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $sourceLang = strtolower($input->getArgument('sourceLang'));
        // If targetLang is missing, translate to all locales except the source
        $targetLang = $input->getArgument('targetLang')
        ? [strtolower($input->getArgument('targetLang'))]
        : array_diff(array_map('strtolower', $this->allLocales), [$sourceLang]);

        $this->skipExistingTranslations = $input->getOption('skip-existing');
        $domain                         = $input->getOption('domain');
        $forceUpdate                    = $input->getOption('force');

        // Map/validate the source language for DeepL
        $deepLSourceLang = $this->deepLSupportedLanguages[$sourceLang] ?? $sourceLang;
        if (! in_array($deepLSourceLang, $this->getDeepLSupportedLanguages(), true)) {
            $io->error("Invalid source language: $deepLSourceLang. Must be a valid DeepL-supported language.");
            return Command::FAILURE;
        }

        // First, estimate total translations across all target languages
        $totalTranslations = $this->estimateTotalTranslations($targetLang, $domain, $deepLSourceLang);
        $progressBar       = new ProgressBar($output, $totalTranslations);
        $this->initializeProgressBar($progressBar);
        $progressBar->start();

        // Translate for each target language
        foreach ($targetLang as $lang) {
            $deepLTargetLang = $this->deepLSupportedLanguages[$lang] ?? $lang;
            if (! in_array($deepLTargetLang, $this->getDeepLSupportedLanguages(), true)) {
                $io->warning("Skipping unsupported target language: $lang (DeepL equivalent: $deepLTargetLang)");
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
     * Estimate total translations for each target language,
     * using the same pattern for the source language.
     */
    private function estimateTotalTranslations(array $targetLang, ?string $domain, string $sourceLang): int
    {
        $totalTranslations = 0;

        foreach ($targetLang as $lang) {
            // Use the pattern to find .yaml/.yml files for the *source* language
            $pattern = $domain ? "{$domain}.{$sourceLang}.*" : "*.$sourceLang.*";

            $finder = new Finder();
            $finder->in($this->translationsDir)
                ->files()
                ->name($pattern);

            foreach ($finder as $file) {
                // Only parse if it's .yml or .yaml
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
        // Pattern for the *source* language
        $pattern = $domain ? "{$domain}.{$sourceLang}.*" : "*.$sourceLang.*";

        $finder = new Finder();
        $finder->in($this->translationsDir)
            ->files()
            ->name($pattern);

        foreach ($finder as $file) {
            // Skip non-YAML
            if (! preg_match('/\.(yml|yaml)$/i', $file->getFilename())) {
                continue;
            }

            $this->parsedFiles++;
            $sourceFile    = $file->getRealPath();
            $targetFile    = str_replace(".$sourceLang.", ".$targetLang.", $sourceFile);
            $sourceContent = Yaml::parseFile($sourceFile);
            $targetContent = file_exists($targetFile) ? Yaml::parseFile($targetFile) : [];

            if (! is_array($sourceContent)) {
                continue; // If the YAML is empty or invalid
            }

            foreach ($sourceContent as $key => $value) {
                // Skip null or empty
                if (! is_string($value) || trim($value) === '') {
                    $io->note("Skipping empty or null key: $key");
                    $this->unchangedKeys++;
                    continue;
                }

                if (isset($targetContent[$key])) {
                    // If skipping existing translations or not forced, ask confirmation
                    if (
                        $this->skipExistingTranslations
                        || (
                            ! $forceUpdate
                            && ! $io->confirm(
                                "Key '$key' has an existing translation. Update? Existing: '{$targetContent[$key]}'",
                                false
                            )
                        )
                    ) {
                        $this->unchangedKeys++;
                        continue;
                    }
                    $this->existingKeysUpdated++;
                } else {
                    $this->newKeysAdded++;
                }

                // Perform the translation
                $targetContent[$key] = $this->deepLTranslationService->translate(
                    $value,
                    $sourceLang,
                    $targetLang
                );

                $this->updateProgressBar($progressBar);
            }

            file_put_contents($targetFile, Yaml::dump($targetContent, 4, 2));
        }
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
     * Returns valid DeepL language codes for reference.
     */
    private function getDeepLSupportedLanguages(): array
    {
        return [
            'en', 'de', 'fr', 'es', 'it', 'nl', 'pl', 'ru',
            'ja', 'zh', 'cs', 'pt', 'sv', 'da', 'fi', 'hu',
        ];
    }
}
