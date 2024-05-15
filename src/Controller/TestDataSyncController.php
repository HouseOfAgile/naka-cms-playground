<?php

namespace HouseOfAgile\NakaCMSBundle\Controller;

use App\Entity\WebsiteInfo;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use HouseOfAgile\NakaCMSBundle\Component\DumperUpdater\DataSyncManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/test-data-sync', name: 'naka_test_')]
class TestDataSyncController extends AbstractController
{
    protected DataSyncManager $dataSyncManager;
    protected ManagerRegistry $doctrine;
    protected array $entitiesToTest = [];

    public function __construct(DataSyncManager $dataSyncManager, ManagerRegistry $doctrine)
    {
        $this->dataSyncManager = $dataSyncManager;
        $this->doctrine = $doctrine;
    }

    protected function setEntitiesToTest(array $entities): void
    {
        $this->entitiesToTest = $entities;
    }

    #[Route('/quick', name: 'quick')]
    public function quickTest(): Response
    {
        $websiteInfoRepository = $this->doctrine->getRepository(WebsiteInfo::class);
        $wsi = $websiteInfoRepository->find(1);
        $data = [
            'dyna' => $this->dataSyncManager->dynamicDump($wsi),
            'dump' => $wsi->dumpConfig()
        ];

        $viewParams = ['data' => $data];
        return $this->render('@NakaCMS/test/data-sync/quick.html.twig', $viewParams);
    }

    #[Route('/entity-dump', name: 'entity_dump')]
    public function entityDumpTest(): Response
    {
        $dumpOutputs = [];

        dump($this->entitiesToTest);
        foreach ($this->entitiesToTest as $entityName) {
            $repository = $this->doctrine->getRepository('App\Entity\\' . $entityName);
            $entities = $repository->findBy([], null, 5);

            foreach ($entities as $entity) {
                $configOutput = $this->prepareDataForDisplay($entity->dumpConfig());
                $dynamicDumpOutput = $this->prepareDataForDisplay($this->dataSyncManager->dynamicDump($entity));
                $differences = $this->computeDifferences($configOutput, $dynamicDumpOutput);

                $dumpOutputs[$entityName][] = [
                    'configOutput' => $configOutput,
                    'dynamicDumpOutput' => $dynamicDumpOutput,
                    'differences' => $differences
                ];
            }
        }

        $viewParams = ['dumpOutputs' => $dumpOutputs];
        return $this->render('@NakaCMS/test/data-sync/entity_dump_details.html.twig', $viewParams);
    }

    #[Route('/entity-dump/{entity}', name: 'entity_random_dump')]
    public function randomEntityDump(string $entity): Response
    {
        $repository = $this->doctrine->getRepository('App\Entity\\' . ucfirst($entity));
        if (!$repository) {
            return $this->render('error.html.twig', ['message' => 'Entity not found']);
        }

        $randomInstance = $repository->findRandom();

        if (!$randomInstance) {
            return $this->render('error.html.twig', ['message' => 'No instance found']);
        }

        $dumpOutput = [
            'configOutput' => $randomInstance->dumpConfig(),
            'dynamicDumpOutput' => $this->dataSyncManager->dynamicDump($randomInstance)
        ];

        return $this->render('@NakaCMS/test/data-sync/entity_random_dump.html.twig', [
            'entityName' => $entity,
            'dumpOutput' => $dumpOutput
        ]);
    }

    protected function prepareDataForDisplay($data)
    {
        foreach ($data as $key => $value) {
            if ($value instanceof \DateTimeImmutable) {
                $data[$key] = $value->format('Y-m-d H:i:s');
            } elseif (is_array($value)) {
                $data[$key] = $this->prepareDataForDisplay($value);
            }
        }
        return $data;
    }

    protected function computeDifferences($configOutput, $dynamicDumpOutput)
    {
        $differences = [];
        $allKeys = array_unique(array_merge(array_keys($configOutput), array_keys($dynamicDumpOutput)));

        foreach ($allKeys as $key) {
            $configValue = $configOutput[$key] ?? null;
            $dynamicValue = $dynamicDumpOutput[$key] ?? null;
            if ($configValue !== $dynamicValue) {
                $differences[$key] = [
                    'config' => $configValue,
                    'dynamic' => $dynamicValue
                ];
            }
        }

        return $differences;
    }
}
