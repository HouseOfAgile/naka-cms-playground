<?php

namespace HouseOfAgile\NakaCMSBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This command provides a comprehensive overview of various aspects of the project,
 * including detailed information about a specified entity, main Composer libraries,
 * and main Node.js libraries.
 */
class ProjectInsightCommand extends Command
{
    protected static $defaultName = 'app:project-insight';

    private $entityManager;
    private $projectDir;

    public function __construct(EntityManagerInterface $entityManager, string $projectDir)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->projectDir = $projectDir;
    }

    protected function configure()
    {
        $this
            ->setDescription('Provides insights into the Symfony project, including entity details, Composer, and Node.js libraries.')
            ->setHelp('This command allows you to get details about a specific entity and lists the main Composer and Node.js libraries used in the project.')
            ->addArgument('entityName', InputArgument::REQUIRED, 'The full class name of the entity to inspect')
            ->addOption('show-composer', null, InputOption::VALUE_NONE, 'Show main Composer libraries')
            ->addOption('show-node', null, InputOption::VALUE_NONE, 'Show main Node.js libraries');;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $entityName = $input->getArgument('entityName');
        $metadata = $this->entityManager->getClassMetadata($entityName);

        $io->title("Entity Details for $entityName");

        // Display entity details
        $this->displayEntityDetails($io, $metadata);

        // Display main Composer libraries
        if ($input->getOption('show-composer')) {
            $this->showComposerLibraries($io);
        }

        // Display main Node.js libraries
        if ($input->getOption('show-node')) {
            $this->showNodeLibraries($io);
        }

        return Command::SUCCESS;
    }

    private function getAssociationType($type)
    {
        switch ($type) {
            case \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_ONE:
                return 'OneToOne';
            case \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY:
                return 'OneToMany';
            case \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE:
                return 'ManyToOne';
            case \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY:
                return 'ManyToMany';
            default:
                return 'Unknown';
        }
    }

    private function showCustomRepositoryMethods(SymfonyStyle $io, $metadata)
    {
        $repositoryClass = $metadata->customRepositoryClassName;

        if (!$repositoryClass) {
            $io->section('Custom Repository Methods');
            $io->text('No custom repository defined.');
            return;
        }

        $reflClass = new \ReflectionClass($repositoryClass);
        $methods = array_filter($reflClass->getMethods(), function ($method) use ($reflClass) {
            return $method->class === $reflClass->getName() && $method->isPublic();
        });

        $io->section('Custom Repository Methods');
        if (empty($methods)) {
            $io->text("No public methods found in custom repository $repositoryClass.");
        } else {
            foreach ($methods as $method) {
                $io->text("- {$method->name}");
            }
        }
    }

    /**
     * Display details about the specified entity, including fields, associations, and custom repository methods.
     */
    private function displayEntityDetails(SymfonyStyle $io, $metadata)
    {
        // Fields
        $io->section('Fields');
        foreach ($metadata->fieldMappings as $field) {
            $io->text("- {$field['fieldName']} ({$field['type']})");
        }

        // Associations
        $io->section('Associations');
        foreach ($metadata->associationMappings as $association) {
            $type = $this->getAssociationType($association['type']);
            $targetEntity = $association['targetEntity'];
            $io->text("- {$association['fieldName']} ({$type} with {$targetEntity})");
        }

        // Custom Repository Methods (if any)
        $this->showCustomRepositoryMethods($io, $metadata);
    }

    /**
     * Show the main Composer libraries used in the project by reading the composer.json file.
     */

    private function showComposerLibraries(SymfonyStyle $io)
    {
        $composerJsonPath = $this->projectDir . '/composer.json';
        if (!file_exists($composerJsonPath)) {
            $io->section('Composer Libraries');
            $io->warning('composer.json not found.');
            return;
        }

        $composerJson = json_decode(file_get_contents($composerJsonPath), true);
        $libraries = $composerJson['require'] ?? [];

        $io->section('Main Composer Libraries');
        foreach ($libraries as $library => $version) {
            $io->text("- $library: $version");
        }
    }

    /**
     * Show the main Node.js libraries used in the project by reading the package.json file.
     */
    private function showNodeLibraries(SymfonyStyle $io)
    {
        $packageJsonPath = $this->projectDir . '/package.json';
        if (!file_exists($packageJsonPath)) {
            $io->section('Node Libraries');
            $io->warning('package.json not found.');
            return;
        }

        $packageJson = json_decode(file_get_contents($packageJsonPath), true);
        $libraries = $packageJson['dependencies'] ?? [];

        $io->section('Main Node Libraries');
        foreach ($libraries as $library => $version) {
            $io->text("- $library: $version");
        }
    }
}
