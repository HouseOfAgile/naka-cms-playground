<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Entity\Picture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Repository\PageGalleryRepository;

#[ORM\MappedSuperclass(repositoryClass: PageGalleryRepository::class)]
class PageGallery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected $name;

    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'pageGallery', cascade: ['remove', 'persist'])]
    protected $images;

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
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];

        if (count($this->getImages()) > 0) {
            $config['galleryImages'] = array_map(function ($image) {
                return $image->getId();
            }, $this->getImages()->toArray());
        }
        return $config;
    }

    public function __toString()
    {
        return sprintf(
            '%s [PageGallery %s]',
            $this->getName() ? $this->getName() . ' ' : '',
            $this->getId(),
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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
