<?php

namespace HouseOfAgile\NakaCMSBundle\Component\NakaParameter;

use App\Repository\NakaParameterRepository;
use Doctrine\ORM\EntityManagerInterface;

class NakaParameterManager
{
    const NAKA_SOCIAL_PAGE_LINK = 'social-page-link.';
    const NAKA_PARAMETER_SOCIAL_PLATFORM = [
        'instagram', 'twitter', 'youtube', 'linkedin'
    ];

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

    public function getAllNakaSocialParameters()
    {
        $existingNakaSocialParamters = [];
        foreach (self::NAKA_PARAMETER_SOCIAL_PLATFORM as $socialPlatform) {
            if ($nakaParameter = $this->getNakaParameter(self::NAKA_SOCIAL_PAGE_LINK . $socialPlatform)) {
                $existingNakaSocialParamters[$socialPlatform] = $nakaParameter->getValue();
            }
        }
        return $existingNakaSocialParamters;
    }

    public function getNakaParameter($path)
    {
        return $this->nakaParameterRepository->findOneBy(['path' => $path]);
    }
}
