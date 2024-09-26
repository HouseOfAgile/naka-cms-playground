<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Doctrine\ORM\EntityManagerInterface;
use HouseOfAgile\NakaCMSBundle\Entity\NakaSiteSettings;

class NakaSiteSettingsExtension extends AbstractExtension
{
    private EntityManagerInterface $entityManager;
    private ?NakaSiteSettings $nakaSiteSettings = null;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getNakaSiteSettings', [$this, 'getNakaSiteSettings']),
        ];
    }

    public function getNakaSiteSettings(): ?NakaSiteSettings
    {
        if ($this->nakaSiteSettings === null) {
            $this->nakaSiteSettings = $this->entityManager
                ->getRepository(NakaSiteSettings::class)
                ->find(1);
        }

        return $this->nakaSiteSettings;
    }
}
