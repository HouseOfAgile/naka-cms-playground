<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use HouseOfAgile\NakaCMSBundle\Repository\PageGalleryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PageGalleryRepository::class)
 */
class PageGallery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="pageGallery",cascade={"persist"})
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * dumpConfig: return array with main config
     *
     * @return array
     */
    public function dumpConfig(): array
    {
        $config =  [
            'id' => $this->id,
        ];
        return $config;

        if (count($this->getImages()) > 0) {
            $config['images'] = array_map(function ($image) {
                return $image->getId();
            }, $this->getImages()->toArray());
        }
        return $config;
    }

    public function __toString()
    {
        return (string)'PageGallery ' . $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $image->setPageGallery($this);
        }

        return $this;
    }

    public function removeImage(Picture $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getPageGallery() === $this) {
                $image->setPageGallery(null);
            }
        }

        return $this;
    }
}
