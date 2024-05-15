<?php

declare(strict_types=1);

namespace HouseOfAgile\NakaCMSBundle\Command;

use App\NakaData\DataDumperParameter;
use Exception;
use HouseOfAgile\NakaCMSBundle\Component\DumperUpdater\DataSyncManager;
use HouseOfAgile\NakaCMSBundle\Helper\LoggerCommandTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ManageInstanceCommand extends Command
{
    use LoggerCommandTrait;

    protected static $defaultName = 'naka:instance:manage';

    /** @var DataSyncManager */
    protected $dataSyncManager;

    public function __construct(DataSyncManager $dataSyncManager)
    {
        parent::__construct();
        $this->dataSyncManager = $dataSyncManager;
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Manage instances config')
            ->addArgument(
                'mainAction',
                InputArgument::REQUIRED,
                'One of the actions: [dump or update]'
            )
            ->addArgument(
                'doNotMoveAsset',
                InputArgument::OPTIONAL,
                'Do not move asset during dump (true or false)',
                false
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->dataSyncManager->setIo($this->io);

        if (class_exists(DataDumperParameter::class)) {
            $appEntitiesAliases = DataDumperParameter::APP_ENTITIES_ALIASES;
            $assetEntities = DataDumperParameter::ASSET_ENTITIES;
            $appEntities = DataDumperParameter::APP_ENTITIES;
        } else {
            throw new Exception('App\Entity\DataDumperParameter Class not Present');
        }

        $dumpOrUpdate = $input->getArgument('mainAction') === 'dump';
        $doNotMoveAsset = filter_var($input->getArgument('doNotMoveAsset'), FILTER_VALIDATE_BOOLEAN);

        $synchronizationStatus = $this->dataSyncManager->manageNakaCMS($appEntities, $appEntitiesAliases, $assetEntities, $dumpOrUpdate, $doNotMoveAsset);

        return $synchronizationStatus ? Command::SUCCESS : Command::FAILURE;
    }
}
