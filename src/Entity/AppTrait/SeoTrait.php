<?php

namespace HouseOfAgile\NakaCMSBundle\Entity\AppTrait;

use Doctrine\ORM\Mapping as ORM;

trait SeoTrait
{
    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $metaTitle = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $metaDescription = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $metaKeywords = null;

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;
        return $this;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(?string $metaKeywords): self
    {
        $this->metaKeywords = $metaKeywords;
        return $this;
    }
}
