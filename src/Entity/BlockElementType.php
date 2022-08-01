<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Entity\BlockElement;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Repository\BlockElementTypeRepository;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\MappedSuperclass(repositoryClass=BlockElementTypeRepository::class)
 */
class BlockElementType implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=40)
     */
    protected $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $htmlCode;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $jsCode;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $cssCode;

    /**
     * @ORM\OneToMany(targetEntity=BlockElement::class, mappedBy="blockElementType")
     */
    protected $blockElements;

    public function __construct()
    {
        $this->blockElements = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf(
            'BlockElementType #%s of type %s',
            $this->id,
            $this->getType()
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
            'type' => $this->getType(),
            'htmlCode' => $this->getHtmlCode(),
            'cssCode' => $this->getCssCode(),
            'jsCode' => $this->getJsCode(),
        ];

        return $config;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->__toString();
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getHtmlCode(): ?string
    {
        return $this->htmlCode;
    }

    public function setHtmlCode(?string $htmlCode): self
    {
        $this->htmlCode = $htmlCode;

        return $this;
    }

    public function getJsCode(): ?string
    {
        return $this->jsCode;
    }

    public function setJsCode(?string $jsCode): self
    {
        $this->jsCode = $jsCode;

        return $this;
    }

    public function getCssCode(): ?string
    {
        return $this->cssCode;
    }

    public function setCssCode(?string $cssCode): self
    {
        $this->cssCode = $cssCode;

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
            $blockElement->setBlockElementType($this);
        }

        return $this;
    }

    public function removeBlockElement(BlockElement $blockElement): self
    {
        if ($this->blockElements->removeElement($blockElement)) {
            // set the owning side to null (unless already changed)
            if ($blockElement->getBlockElementType() === $this) {
                $blockElement->setBlockElementType(null);
            }
        }

        return $this;
    }
}
