<?php

declare(strict_types=1);

namespace HouseOfAgile\NakaCMSBundle\Command;

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
            'typeOfContent',
            InputArgument::REQUIRED,
            'Working with content or assets [content or assets]'
        );
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

        $appEntitiesAliases = [
            'pageTranslations' => ['class' => 'PageTranslation', 'method' => 'translation'],
            'staticPageTranslations' => ['class' => 'StaticPageTranslation', 'method' => 'translation'],
            'nakaEventTranslations' => ['class' => 'NakaEventTranslation', 'method' => 'translation'],
            'menuItems' => 'MenuItem',
            'pageBlockElements' => 'PageBlockElement',
            'blockElement' => 'BlockElement',
            'blockElementType' => 'BlockElementType',
            'staticPage' => 'StaticPage',
            'page' => 'Page',
            'pictures' => 'Picture',
            'gallery' => 'Gallery',
        ];
        // $assetEntities is a list of entities that are with uploaded content
        $assetEntities = [
            // 'Picture',
        ];

        $appEntities = [
            'PageTranslation' => [],
            'PageGallery' => [],
            // 'User' => [],
            // 'AdminUser' => [],
            'StaticPageTranslation' => [],
            'StaticPage' => [],
            'Page' => [],
            'MenuItem' => [],
            'Menu' => [],
            // 'Picture' => [],
            'Gallery' => [],
            'BlockElementType' => [],
            'BlockElement' => [],
            'PageBlockElement' => [],
            // 'Contact' => [],
            // 'NakaEventTranslation' => [],
            // 'NakaEvent' => ['beginDate' => 'DateTime', 'endDate' => 'DateTime',],
        ];

        $dumpOrUpdate = $input->getArgument('mainAction') == 'dump';
        $contentOrAsset = $input->getArgument('typeOfContent') == 'content';

        if ($contentOrAsset) {
            $synchronizationStatus = $this->dumperUpdater->synchronizeData($appEntities, $appEntitiesAliases, $assetEntities, $dumpOrUpdate);
        } else {
            $synchronizationStatus = $this->dumperUpdater->synchronizeAssets($assetEntities, $dumpOrUpdate);
        }

        return $synchronizationStatus ? Command::SUCCESS : Command::FAILURE;
    }
}
