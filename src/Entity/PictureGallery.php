<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Entity\Picture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Repository\PictureGalleryRepository;

#[ORM\MappedSuperclass(repositoryClass: PictureGalleryRepository::class)]
class PictureGallery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'pictureGallery')]
    protected $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) 'PictureGallery ' . $this->id;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Picture $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setPictureGallery($this);
        }

        return $this;
    }

    public function removeImage(Picture $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getPictureGallery() === $this) {
                $image->setPictureGallery(null);
            }
        }

        return $this;
    }
}
