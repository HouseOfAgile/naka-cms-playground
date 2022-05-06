<?php

declare(strict_types=1);

namespace HouseOfAgile\NakaCMSBundle\Command;

use App\NakaData\DataDumperParameter;
use Exception;
use HouseOfAgile\NakaCMSBundle\Component\DumperUpdater\DumperUpdater;
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

    /** @var DumperUpdater */
    protected $dumperUpdater;

    public function __construct(
        DumperUpdater $dumperUpdater
    ) {
        parent::__construct();
        $this->dumperUpdater = $dumperUpdater;
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Manage instances config');
        $this->addArgument(
            'mainAction',
            InputArgument::REQUIRED,
            'One of the actions: [dump or update]'
        );
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->dumperUpdater->setIo(
            $this->io
        );

        if (class_exists(DataDumperParameter::class)) {

            $appEntitiesAliases = DataDumperParameter::APP_ENTITIES_ALIASES;;
            // $assetEntities is a list of entities that are with uploaded content
            $assetEntities = DataDumperParameter::ASSET_ENTITIES;

            $appEntities = DataDumperParameter::APP_ENTITIES;
        } else {
            throw new Exception('App\Entity\DataDumperParameter Class not Present');
        }

        $dumpOrUpdate = $input->getArgument('mainAction') == 'dump';

        $synchronizationStatus = $this->dumperUpdater->manageNakaCMS($appEntities, $appEntitiesAliases, $assetEntities, $dumpOrUpdate);

        return $synchronizationStatus ? Command::SUCCESS : Command::FAILURE;
    }
}
