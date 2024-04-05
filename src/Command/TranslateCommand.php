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
    private int $parsedFiles = 0;
    private int $newKeysAdded = 0;
    private int $existingKeysUpdated = 0;
    private int $unchangedKeys = 0;

    public function __construct(DeepLTranslationService $deepLTranslationService, string $translationsDir, array $allLocales)
    {
        $this->deepLTranslationService = $deepLTranslationService;
        $this->translationsDir = $translationsDir;
        $this->allLocales = $allLocales;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('sourceLang', InputArgument::REQUIRED, 'The source language code.')
            ->addArgument('targetLang', InputArgument::OPTIONAL, 'The target language code, translates to all if not set.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force update all translations without confirmation.')
            ->addOption('skip-existing', null, InputOption::VALUE_NONE, 'Skip updating existing translations.')
            ->addOption('domain', null, InputOption::VALUE_OPTIONAL, 'The domain to translate, translates all if not set.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $sourceLang = strtolower($input->getArgument('sourceLang'));
        $targetLang = $input->getArgument('targetLang') ? [strtolower($input->getArgument('targetLang'))] : array_map('strtolower', $this->allLocales);
        $this->skipExistingTranslations = $input->getOption('skip-existing');
        $domain = $input->getOption('domain');
        $forceUpdate = $input->getOption('force');
        $targetLang = array_diff($targetLang, [$sourceLang]);

        // Estimate the total number of translations to process for the progress bar initialization
        $totalTranslations = $this->estimateTotalTranslations($targetLang, $domain, $sourceLang);
        $progressBar = new ProgressBar($output, $totalTranslations);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% Memory: %memory:6s% | New: %newKeys%, Updated: %updatedKeys%, Skipped: %skippedKeys%');
        $progressBar->setMessage('0', 'newKeys');
        $progressBar->setMessage('0', 'updatedKeys');
        $progressBar->setMessage('0', 'skippedKeys');
        $progressBar->start();

        foreach ($targetLang as $lang) {
            $this->translateLanguage($sourceLang, $lang, $io, $domain, $forceUpdate, $progressBar);
        }

        $progressBar->finish();
        $io->newLine(2);
        $io->success('Translation process completed.');
        $this->displayStatistics($io);

        return Command::SUCCESS;
    }

    private function estimateTotalTranslations(array $targetLang, ?string $domain, string $sourceLang): int
    {
        $totalTranslations = 0;
        foreach ($targetLang as $lang) {
            $finder = new Finder();
            $pattern = $domain ? "{$domain}.{$sourceLang}.{yaml,yml}" : "*.$sourceLang.{yaml,yml}";
            $finder->in($this->translationsDir)->files()->name($pattern);
            foreach ($finder as $file) {
                $sourceContent = Yaml::parseFile($file->getRealPath());
                $totalTranslations += count($sourceContent);
            }
        }
        return $totalTranslations;
    }

    private function translateLanguage(string $sourceLang, string $targetLang, SymfonyStyle $io, ?string $domain, bool $forceUpdate, ProgressBar $progressBar): void
    {
        $finder = new Finder();
        $pattern = $domain ? "{$domain}.{$sourceLang}.{yaml,yml}" : "*.$sourceLang.{yaml,yml}";
        $finder->in($this->translationsDir)->files()->name($pattern);

        foreach ($finder as $file) {
            $this->parsedFiles++;
            $sourceFile = $file->getRealPath();
            $targetFilePattern = str_replace(".$sourceLang.", ".$targetLang.", $sourceFile);
            $targetFile = file_exists(str_replace('.yml', '.yaml', $targetFilePattern)) ? str_replace('.yml', '.yaml', $targetFilePattern) : str_replace('.yaml', '.yml', $targetFilePattern);
            $sourceContent = Yaml::parseFile($sourceFile);
            $targetContent = file_exists($targetFile) ? Yaml::parseFile($targetFile) : [];

            foreach ($sourceContent as $key => $value) {
                if (isset($targetContent[$key])) {
                    if ($this->skipExistingTranslations || (!$forceUpdate && !$io->confirm(sprintf("Key '%s' has an existing translation. Update? Existing: '%s'", $key, $targetContent[$key]), false))) {
                        $this->unchangedKeys++;
                        continue;
                    }
                    $this->existingKeysUpdated++;
                } else {
                    $this->newKeysAdded++;
                }

                $targetContent[$key] = $this->deepLTranslationService->translate($value, $sourceLang, $targetLang);
                $progressBar->setMessage((string) $this->newKeysAdded, 'newKeys');
                $progressBar->setMessage((string) $this->existingKeysUpdated, 'updatedKeys');
                $progressBar->setMessage((string) $this->unchangedKeys, 'skippedKeys');
                $progressBar->advance();
            }

            file_put_contents($targetFile, Yaml::dump($targetContent, 4, 2));
        }
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
}
