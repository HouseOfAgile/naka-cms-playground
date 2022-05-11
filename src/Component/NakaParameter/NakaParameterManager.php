<?php

namespace HouseOfAgile\NakaCMSBundle\Component\NakaParameter;

use App\Repository\NakaParameterRepository;
use Doctrine\ORM\EntityManagerInterface;

class NakaParameterManager
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var NakaParameterRepository */
    protected $nakaParameterRepository;


    public function __construct(
        EntityManagerInterface $entityManager,
        NakaParameterRepository $nakaParameterRepository
    ) {
        $this->entityManager = $entityManager;
        $this->nakaParameterRepository = $nakaParameterRepository;
    }

    public function getAllParameter()
    {
        return $this->nakaParameterRepository->findAll();
    }

    public function getNakaParameter($path)
    {
        return $this->nakaParameterRepository->findOneBy(['path' => $path]);
    }
}
