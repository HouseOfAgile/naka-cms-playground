<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Repository\NakaSiteSettingsRepository;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

#[Vich\Uploadable]
#[ORM\MappedSuperclass(repositoryClass: NakaSiteSettingsRepository::class)]
class NakaSiteSettings implements TimestampableInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    protected int $id = 1;

    // Logo Image File
    #[UploadableField(
        mapping: 'site_images',
        fileNameProperty: 'logoImage.name',
        size: 'logoImage.size',
        mimeType: 'logoImage.mimeType',
        originalName: 'logoImage.originalName',
        dimensions: 'logoImage.dimensions'
    )]
    protected ?File $logoImageFile = null;

    #[ORM\Embedded(class: EmbeddedFile::class)]
    protected EmbeddedFile $logoImage;

    // Transparent Logo Image File
    #[UploadableField(
        mapping: 'site_images',
        fileNameProperty: 'logoTransparentImage.name',
        size: 'logoTransparentImage.size',
        mimeType: 'logoTransparentImage.mimeType',
        originalName: 'logoTransparentImage.originalName',
        dimensions: 'logoTransparentImage.dimensions'
    )]
    protected ?File $logoTransparentImageFile = null;

    #[ORM\Embedded(class: EmbeddedFile::class)]
    protected EmbeddedFile $logoTransparentImage;

    // Default Background Image File
    #[UploadableField(
        mapping: 'site_images',
        fileNameProperty: 'defaultBackgroundImage.name',
        size: 'defaultBackgroundImage.size',
        mimeType: 'defaultBackgroundImage.mimeType',
        originalName: 'defaultBackgroundImage.originalName',
        dimensions: 'defaultBackgroundImage.dimensions'
    )]
    protected ?File $defaultBackgroundImageFile = null;

    #[ORM\Embedded(class: EmbeddedFile::class)]
    protected EmbeddedFile $defaultBackgroundImage;

    public function __construct()
    {
        $this->logoImage = new EmbeddedFile();
        $this->logoTransparentImage = new EmbeddedFile();
        $this->defaultBackgroundImage = new EmbeddedFile();
    }

    // Getters and Setters

    // ID
    public function getId(): int
    {
        return $this->id;
    }

	public function __toString()
    {
        return sprintf(
            'NakaSiteSettings [%s]: %s',
            $this->getId(),
            $this->getLogoImage()->getName(),
        );
    }

    // Logo Image
    public function setLogoImageFile(?File $file = null): void
    {
        $this->logoImageFile = $file;

        if (null !== $file) {
            $this->setUpdatedAt(new \DateTimeImmutable());
        }
    }

    public function getLogoImageFile(): ?File
    {
        return $this->logoImageFile;
    }

    public function getLogoImage(): EmbeddedFile
    {
        return $this->logoImage;
    }

    public function setLogoImage(EmbeddedFile $logoImage): void
    {
        $this->logoImage = $logoImage;
    }

    // Transparent Logo Image
    public function setLogoTransparentImageFile(?File $logoTransparentImageFile = null): void
    {
        $this->logoTransparentImageFile = $logoTransparentImageFile;

        if (null !== $logoTransparentImageFile) {
            $this->setUpdatedAt(new \DateTimeImmutable());
        }
    }

    public function getLogoTransparentImageFile(): ?File
    {
        return $this->logoTransparentImageFile;
    }

    public function getLogoTransparentImage(): EmbeddedFile
    {
        return $this->logoTransparentImage;
    }

    public function setLogoTransparentImage(EmbeddedFile $logoTransparentImage): void
    {
        $this->logoTransparentImage = $logoTransparentImage;
    }

    // Default Background Image
    public function setDefaultBackgroundImageFile(?File $defaultBackgroundImageFile = null): void
    {
        $this->defaultBackgroundImageFile = $defaultBackgroundImageFile;

        if (null !== $defaultBackgroundImageFile) {
            $this->setUpdatedAt(new \DateTimeImmutable());
        }
    }

    public function getDefaultBackgroundImageFile(): ?File
    {
        return $this->defaultBackgroundImageFile;
    }

    public function getDefaultBackgroundImage(): EmbeddedFile
    {
        return $this->defaultBackgroundImage;
    }

    public function setDefaultBackgroundImage(EmbeddedFile $defaultBackgroundImage): void
    {
        $this->defaultBackgroundImage = $defaultBackgroundImage;
    }
}
