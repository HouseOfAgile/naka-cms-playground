<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Entity\BlockElementType;
use App\Entity\Gallery;
use App\Entity\PageBlockElement;
use App\Entity\Picture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Repository\BlockElementRepository;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\MappedSuperclass
 */
class BlockElement implements TimestampableInterface
{

    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity=BlockElementType::class, inversedBy="blockElements")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $blockElementType;


    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $name;

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
     * @ORM\ManyToMany(targetEntity=Picture::class, inversedBy="blockElements")
     */
    protected $pictures;

    /**
     * @ORM\OneToMany(targetEntity=PageBlockElement::class, mappedBy="blockElement")
     */
    protected $pageBlockElements;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $blockConfig = [];

    /**
     * @ORM\ManyToOne(targetEntity=Gallery::class, inversedBy="blockElements")
     */
    protected $gallery;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $isFluid;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->pageBlockElements = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf(
            'BlockElement %s %s [%s]',
            $this->getId(),
            $this->getName() ?? $this->getId(),
            // @todo fix this
            $this->getBlockElementType() ? $this->getBlockElementType()->getType(): 'unset'
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
            'name' => $this->getName(),
            'htmlCode' => $this->getHtmlCode(),
            'cssCode' => $this->getCssCode(),
            'jsCode' => $this->getJsCode(),
            'isFluid' => $this->getIsFluid(),
            'blockConfig' => $this->getblockConfig(),
            'gallery' => $this->getGallery() ? $this->getGallery()->getId() : null,
            // 'pageBlockElements' => array_map(function ($pbe) {
            //     return $pbe->getId();
            // }, $this->getPageBlockElements()->toArray()),
        ];

        if ($this->getBlockElementType() != null) {
            $config['blockElementType'] = $this->getBlockElementType()->getId();
        }

        return $config;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * getType: return type of blockElement if blockElementType is set, unset otherwise
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->getBlockElementType() ? $this->getBlockElementType()->getType() : 'unset';
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

    public function getBlockElementType(): ?BlockElementType
    {
        return $this->blockElementType;
    }

    public function setBlockElementType(?BlockElementType $blockElementType): self
    {
        $this->blockElementType = $blockElementType;

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
     * @return Collection|PageBlockElement[]
     */
    public function getPageBlockElements(): Collection
    {
        return $this->pageBlockElements;
    }

    public function addPageBlockElement(PageBlockElement $pageBlockElement): self
    {
        if (!$this->pageBlockElements->contains($pageBlockElement)) {
            $this->pageBlockElements[] = $pageBlockElement;
            $pageBlockElement->setBlockElement($this);
        }

        return $this;
    }

    public function removePageBlockElement(PageBlockElement $pageBlockElement): self
    {
        if ($this->pageBlockElements->removeElement($pageBlockElement)) {
            // set the owning side to null (unless already changed)
            if ($pageBlockElement->getBlockElement() === $this) {
                $pageBlockElement->setBlockElement(null);
            }
        }

        return $this;
    }

    public function getBlockConfig(): ?array
    {
        return $this->blockConfig;
    }

    public function setBlockConfig(?array $blockConfig): self
    {
        $this->blockConfig = $blockConfig;

        return $this;
    }

    public function getGallery(): ?Gallery
    {
        return $this->gallery;
    }

    public function setGallery(?Gallery $gallery): self
    {
        $this->gallery = $gallery;

        return $this;
    }

    public function getIsFluid(): ?bool
    {
        return $this->isFluid;
    }

    public function setIsFluid(?bool $isFluid): self
    {
        $this->isFluid = $isFluid;

        return $this;
    }
}
