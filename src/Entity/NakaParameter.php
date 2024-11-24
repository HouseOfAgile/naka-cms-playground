<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
class NakaParameter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected $path;

    #[ORM\Column(type: 'text', nullable: true)]
    protected $value;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected $extraInfo;

    public function __toString()
    {
        return sprintf(
            'NakaParameter #%s %s [%s]',
            $this->id,
            $this->path,
            $this->value ?? 'unset'
        );
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getExtraInfo(): ?string
    {
        return $this->extraInfo;
    }

    public function setExtraInfo(?string $extraInfo): self
    {
        $this->extraInfo = $extraInfo;

        return $this;
    }
}
