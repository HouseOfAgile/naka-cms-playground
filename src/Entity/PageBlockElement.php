<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Entity\BlockElement;
use App\Entity\Page;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use HouseOfAgile\NakaCMSBundle\Repository\PageBlockElementRepository;

/**
 * @ORM\MappedSuperclass
 */
class PageBlockElement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Page::class, inversedBy="pageBlockElements")
     */
    private $page;

    /**
     * @ORM\ManyToOne(targetEntity=BlockElement::class, inversedBy="pageBlockElements")
     */
    private $blockElement;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     */
    private $position;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return sprintf(
            'PageBlockElement #%s %s',
            $this->id,
            $this->getBlockElement() ? $this->getBlockElement()->getName() : "noblock"
        );
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
            'position' => $this->getPosition(),
            'blockElement' => $this->getBlockElement() ? $this->getBlockElement()->getId() : null,
            'page' => $this->getPage() ? $this->getPage()->getId() : null,
        ];

        return $config;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getBlockElement(): ?BlockElement
    {
        return $this->blockElement;
    }

    public function setBlockElement(?BlockElement $blockElement): self
    {
        $this->blockElement = $blockElement;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
