<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Entity\Category;
use App\Entity\MenuItem;
use App\Entity\PageBlockElement;
use App\Entity\PageGallery;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Entity\AppTrait\DefaultTranslatableTrait;
use HouseOfAgile\NakaCMSBundle\Repository\PageRepository;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableMethodsTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatablePropertiesTrait;

#[ORM\MappedSuperclass(repositoryClass: PageRepository::class)]
class Page implements TranslatableInterface, SluggableInterface, TimestampableInterface
{
    use TranslatablePropertiesTrait;
    use TranslatableMethodsTrait;
        use SluggableTrait;
    use DefaultTranslatableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(nullable: true)]
    private ?bool $active = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    protected $name;

    #[ORM\Column(type: 'NakaPageType', nullable: true)]
    protected $pageType;

    #[ORM\ManyToOne(inversedBy: 'pages')]
    private ?PageGallery $pageGallery = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'pages')]
    protected $category;

    #[ORM\OneToMany(targetEntity: MenuItem::class, mappedBy: 'page')]
    protected $menuItems;

    #[ORM\OneToMany(targetEntity: PageBlockElement::class, mappedBy: 'page', cascade: ['remove'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    protected $pageBlockElements;


    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->menuItems = new ArrayCollection();
        $this->pageBlockElements = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf(
            'Page #%s %s',
            $this->id,
            $this->getTitle() ? substr($this->getTitle(), 0, 39) : $this->getName()
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function findPosition($operator): ?int
    {
        if (count($this->getPageBlockElements()) > 0) {
            $result = array_map(
                function ($o) {
                    return $o->getPosition();
                },
                $this->getPageBlockElements()->toArray()
            );
            switch ($operator) {
                case 'max':
                    return max($result) + 1;
                    break;
                case 'min':
                    return min($result);
                    break;
                default:
                    return false;
                    break;
            }
        } else {
            return 1;
        }
    }

    protected function shouldRegenerateSlugOnUpdate(): bool
    {
        return false;
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
            'active' => $this->getActive(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
            'pageType' => $this->getPageType(),
            'pageGallery' => $this->getPageGallery() ? $this->getPageGallery()->getId() : null,
            'pageTranslations' => array_map(function ($pt) {
                return $pt->getId();
            }, $this->getTranslations()->toArray()),
        ];

        return $config;
    }

    /**
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['name', 'title'];
    }
    
    public function getTitle()
    {
        return $this->getDefaultEnglishTranslation($this, 'title');
    }

    public function getContent()
    {
        return $this->getDefaultEnglishTranslation($this, 'content');
    }

    public function getOrderedPageBlockElementsArray(): array
    {
        return  array_map(
            function ($o) {
                return $o->getId();
            },
            $this->getPageBlockElements()->toArray()
        );
    }
    public function getPageType()
    {
        return $this->pageType;
    }

    public function setPageType($pageType): self
    {
        $this->pageType = $pageType;

        return $this;
    }

    public function setEnabled(?bool $enabled): self
    {
        return $this;
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

    /**
     * @return Collection|Category[]
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->category->removeElement($category);

        return $this;
    }

    /**
     * @return Collection|MenuItem[]
     */
    public function getMenuItems(): Collection
    {
        return $this->menuItems;
    }

    public function addMenuItem(MenuItem $menuItem): self
    {
        if (!$this->menuItems->contains($menuItem)) {
            $this->menuItems[] = $menuItem;
            $menuItem->setPage($this);
        }

        return $this;
    }

    public function removeMenuItem(MenuItem $menuItem): self
    {
        if ($this->menuItems->removeElement($menuItem)) {
            // set the owning side to null (unless already changed)
            if ($menuItem->getPage() === $this) {
                $menuItem->setPage(null);
            }
        }

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
            $pageBlockElement->setPage($this);
        }

        return $this;
    }

    public function removePageBlockElement(PageBlockElement $pageBlockElement): self
    {
        if ($this->pageBlockElements->removeElement($pageBlockElement)) {
            // set the owning side to null (unless already changed)
            if ($pageBlockElement->getPage() === $this) {
                $pageBlockElement->setPage(null);
            }
        }

        return $this;
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
}
