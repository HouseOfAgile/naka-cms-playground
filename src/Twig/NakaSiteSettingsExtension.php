<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use HouseOfAgile\NakaCMSBundle\Entity\NakaSiteSettings;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Vich\UploaderBundle\Entity\File as VichFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class NakaSiteSettingsExtension extends AbstractExtension
{
    private EntityManagerInterface $entityManager;
    private UploaderHelper $uploaderHelper;
    private ?NakaSiteSettings $nakaSiteSettings = null;

    public function __construct(EntityManagerInterface $entityManager, UploaderHelper $uploaderHelper)
    {
        $this->entityManager = $entityManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getNakaSiteSetting', [$this, 'getNakaSiteSetting']),
        ];
    }

    public function getNakaSiteSetting(string $attribute, string $defaultImagePath = 'images/logo/default_logo.png'): mixed
    {
        if ($this->nakaSiteSettings === null) {
            $this->nakaSiteSettings = $this->entityManager
                ->getRepository(NakaSiteSettings::class)
                ->find(1);
        }
        if ($this->nakaSiteSettings === null) {
            return $defaultImagePath;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $value = $propertyAccessor->getValue($this->nakaSiteSettings, $attribute);

        // If the attribute is an image file and it exists, return its URL using VichUploader from the File attribute 
        if ($value instanceof VichFile) {
            $imageUrl = $this->uploaderHelper->asset($this->nakaSiteSettings, $attribute . 'File');
            if ($imageUrl !== null) {
                return $imageUrl;
            }
		}

        // If no image is set, return the default image path
        return $defaultImagePath;
    }
}
