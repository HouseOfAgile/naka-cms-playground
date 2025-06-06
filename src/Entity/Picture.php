<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Entity\Gallery;
use App\Entity\PageGallery;
use App\Entity\BlockElement;
use App\Entity\PictureGallery;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use HouseOfAgile\NakaCMSBundle\Repository\PictureRepository;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableMethodsTrait;
use HouseOfAgile\NakaCMSBundle\Entity\AppTrait\DefaultTranslatableTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatablePropertiesTrait;

#[Vich\Uploadable]
#[ORM\MappedSuperclass(repositoryClass: PictureRepository::class)]
class Picture implements TimestampableInterface,  TranslatableInterface
{
	use TranslatablePropertiesTrait;
	use TranslatableMethodsTrait;
	use TimestampableTrait;
	use DefaultTranslatableTrait;
	use TranslatableTrait;

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	protected $id;

	#[ORM\Column(type: 'string', length: 64, nullable: true)]
	protected $name;

	/**
	 * NOTE: This is not a mapped field of entity metadata, just a simple property.
	 *
	 * @var File|null
	 */
	#[Vich\UploadableField(
		mapping: 'asset_pictures',
		fileNameProperty: 'image.name',
		size: 'image.size',
		mimeType: 'image.mimeType',
		originalName: 'image.originalName',
		dimensions: 'image.dimensions'
	)]
	protected $imageFile;

	/**
	 * @var EmbeddedFile
	 */
	#[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
	protected $image;

	#[ORM\ManyToOne(targetEntity: PageGallery::class, inversedBy: 'images', cascade: ['persist'])]
	protected $pageGallery;

	#[ORM\ManyToOne(targetEntity: PictureGallery::class, inversedBy: 'images')]
	protected $pictureGallery;

	#[ORM\ManyToMany(targetEntity: BlockElement::class, mappedBy: 'pictures')]
	protected $blockElements;

	#[ORM\ManyToMany(targetEntity: Gallery::class, mappedBy: 'pictures')]
	protected $galleries;

	public function __construct()
	{
		$this->image = new EmbeddedFile();
		$this->blockElements = new ArrayCollection();
		$this->galleries = new ArrayCollection();
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
		$config =  [
			'id' => $this->getId(),
			// 'originalName' => $this->image->getOriginalName(),
			'name' => $this->getName(),
			// 'mimeType' => $this->image->getMimeType(),
			// 'dimensions' => $this->image->getDimensions(),
			// 'size' => $this->image->getSize(),
		];

		return $config;
	}

	public function getAltText(): ?string
	{
		$altText = $this->getDefaultEnglishTranslation($this, 'altText');
		if ($altText !== null) {
			return $altText;
		}

		return $this->getName() ?? 'No name set';
	}

	public function __toString()
	{
		return sprintf(
			'Image [%s]: %s (%s) ',
			$this->getId(),
			$this->getName() ?? 'No name set',
			$this->getImage()->getName(),
		);
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(?string $name): self
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
	 * @param File|UploadedFile|null $imageFile
	 */
	public function setImageFile(?File $imageFile = null)
	{
		$this->imageFile = $imageFile;

		if ($imageFile instanceof UploadedFile) {
			if (empty($this->name)) {
				$this->name = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
			}
			$this->updatedAt = new \DateTimeImmutable();
		}
	}

	public function getImageFile(): ?File
	{
		return $this->imageFile;
	}

	public function setImage(EmbeddedFile $image): void
	{
		$this->image = $image;
	}

	public function getImage(): ?EmbeddedFile
	{
		return $this->image;
	}

	public function getPageGallery(): ?PageGallery
	{
		return $this->pageGallery;
	}

	public function setPageGallery(?PageGallery $pageGallery): self
	{
		$this->pageGallery = $pageGallery;

		return $this;
	}

	public function getPictureGallery(): ?PictureGallery
	{
		return $this->pictureGallery;
	}

	public function setPictureGallery(?PictureGallery $pictureGallery): self
	{
		$this->pictureGallery = $pictureGallery;

		return $this;
	}

	/**
	 * @return Collection|BlockElement[]
	 */
	public function getBlockElements(): Collection
	{
		return $this->blockElements;
	}

	public function addBlockElement(BlockElement $blockElement): self
	{
		if (!$this->blockElements->contains($blockElement)) {
			$this->blockElements[] = $blockElement;
			$blockElement->addPicture($this);
		}

		return $this;
	}

	public function removeBlockElement(BlockElement $blockElement): self
	{
		if ($this->blockElements->removeElement($blockElement)) {
			$blockElement->removePicture($this);
		}

		return $this;
	}

	/**
	 * @return Collection|Gallery[]
	 */
	public function getGalleries(): Collection
	{
		return $this->galleries;
	}

	public function addGallery(Gallery $gallery): self
	{
		if (!$this->galleries->contains($gallery)) {
			$this->galleries[] = $gallery;
			$gallery->addPicture($this);
		}

		return $this;
	}

	public function removeGallery(Gallery $gallery): self
	{
		if ($this->galleries->removeElement($gallery)) {
			$gallery->removePicture($this);
		}

		return $this;
	}
}
