<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\MappedSuperclass]
class WebsiteAsset implements TimestampableInterface
{
	use TimestampableTrait;

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	protected $id;

	#[ORM\Column(type: 'string', length: 64, nullable: true)]
	protected $name;

	#[ORM\Column(type: 'string', length: 255, nullable: true)]
	protected $assetDescription;

	/**
	 * NOTE: This is not a mapped field of entity metadata, just a simple property.
	 *
	 * @Vich\UploadableField(mapping="asset_website_resources", fileNameProperty="asset.name", size="asset.size", mimeType="asset.mimeType", originalName="asset.originalName", dimensions="asset.dimensions")
	 *
	 * @var File|null
	 */
	#[Vich\UploadableField(mapping: 'asset_website_resources', fileNameProperty: 'asset.name', size: 'asset.size', mimeType: 'asset.mimeType', originalName: 'asset.originalName', dimensions: 'asset.dimensions')]
	protected $assetFile;

	/**
	 * @var EmbeddedFile
	 */
	#[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
	protected $asset;

	public function __construct()
	{
		$this->asset = new EmbeddedFile();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * dumpConfig: return array with main config
	 *
	 * @return array
	 */
	public function dumpConfig(): array
	{
		$config = [
			'id' => $this->getId(),
			'name' => $this->getName(),
			'assetDescription' => $this->getAssetDescription(),
		];

		return $config;
	}

	public function __toString()
	{
		return sprintf(
			'Document: %s (%s [#%s])',
			$this->getName(),
			$this->getAsset()->getName(),
			$this->getId(),
		);
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(?string $name): static
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
	 * of 'UploadedFile' is injected into this setter to trigger the  update. If this
	 * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
	 * must be able to accept an instance of 'File' as the bundle will inject one here
	 * during Doctrine hydration.
	 *
	 * @param File|UploadedFile|null $assetFile
	 */
		public function setAssetFile(?File $assetFile = null)
	{
		$this->assetFile = $assetFile;

		if ($assetFile instanceof UploadedFile) {
			if (empty($this->name)) {
				$this->name = pathinfo($assetFile->getClientOriginalName(), PATHINFO_FILENAME);
			}
			$this->updatedAt = new \DateTimeImmutable();
		}
	}

	public function getAssetFile(): ?File
	{
		return $this->assetFile;
	}

	public function setAsset(EmbeddedFile $asset): void
	{
		$this->asset = $asset;
	}

	public function getAsset(): ?EmbeddedFile
	{
		return $this->asset;
	}

	public function getAssetDescription(): ?string
	{
		return $this->assetDescription;
	}

	public function setAssetDescription(?string $assetDescription): static
	{
		$this->assetDescription = $assetDescription;

		return $this;
	}
}
