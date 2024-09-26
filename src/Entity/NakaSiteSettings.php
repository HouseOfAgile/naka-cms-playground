<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Repository\NakaSiteSettingsRepository;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

#[Vich\Uploadable]
#[ORM\MappedSuperclass(repositoryClass: NakaSiteSettingsRepository::class)]
class NakaSiteSettings implements TimestampableInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected int $id;

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

    public function __construct()
    {
        $this->logoImage = new EmbeddedFile();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
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
}
