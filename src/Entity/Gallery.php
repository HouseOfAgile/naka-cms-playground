<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Entity\BlockElement;
use App\Entity\Picture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Repository\GalleryRepository;

/**
 * @ORM\MappedSuperclass(repositoryClass=GalleryRepository::class)
 */
class Gallery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Picture::class, inversedBy="galleries",cascade={"persist"})
     */
    private $pictures;

    /**
     * @ORM\OneToMany(targetEntity=BlockElement::class, mappedBy="gallery")
     */
    protected $blockElements;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->blockElements = new ArrayCollection();
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
            'name' => $this->getname(),
        ];

        if (count($this->getPictures()) > 0) {
            $config['pictures'] = array_map(function ($picture) {
                return $picture->getId();
            }, $this->getPictures()->toArray());
        }
        return $config;
    }

    public function __toString()
    {
        return sprintf(
            'Gallery %s',
            $this->getName() ?? $this->getId()
        );
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection|Picture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        $this->pictures->removeElement($picture);

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
            $blockElement->setGallery($this);
        }

        return $this;
    }

    public function removeBlockElement(BlockElement $blockElement): self
    {
        if ($this->blockElements->removeElement($blockElement)) {
            // set the owning side to null (unless already changed)
            if ($blockElement->getGallery() === $this) {
                $blockElement->setGallery(null);
            }
        }

        return $this;
    }
}
