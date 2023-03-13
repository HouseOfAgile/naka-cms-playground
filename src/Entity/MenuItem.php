<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Entity\Menu;
use App\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use HouseOfAgile\NakaCMSBundle\DBAL\Types\NakaMenuItemType;
use HouseOfAgile\NakaCMSBundle\Entity\AppTrait\DefaultTranslatableTrait;
use HouseOfAgile\NakaCMSBundle\Repository\MenuItemRepository;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

#[ORM\MappedSuperclass(repositoryClass: MenuItemRepository::class)]
class MenuItem implements TranslatableInterface
{
    use TranslatableTrait;
    use DefaultTranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(type: 'string', length: 40)]
    protected $name;

    #[ORM\Column(type: 'string', length: 120, nullable: true)]
    protected $route;

    #[ORM\Column(type: 'array', nullable: true)]
    protected $routeParameters = [];

    #[ORM\ManyToOne(targetEntity: Page::class, inversedBy: 'menuItems')]
    // #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    protected $page;

    #[ORM\Column(type: 'smallint', nullable: true)]
    protected $orderId;

    #[ORM\ManyToMany(targetEntity: Menu::class, mappedBy: 'menuItems')]
    protected $menus;

    #[ORM\Column(type: 'NakaMenuItemType', nullable: true)]
    protected $type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected $uri;

    /**
     * @Gedmo\SortablePosition
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    protected $position;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
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
            'type' => $this->getType(),
            'route' => $this->getRoute(),
            'orderId' => $this->getOrderId(),
            'position' => $this->getPosition(),
            'routeParameters' => $this->getRouteParameters(),
            'menuItemTranslations' => array_map(function ($mi) {
                return $mi->getId();
            }, $this->getTranslations()->toArray()),
        ];
        if ($this->getPage() != null) {
            $config['page'] = $this->getPage()->getId();
        }

        return $config;
    }

    public function __toString()
    {
        return sprintf(
            'MenuItem #%s %s [%s]',
            $this->id,
            $this->name,
            $this->type ?? 'unset'
        );
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->name = '[Copy] ' . $this->getName();
    }

    public function getTitle()
    {
        return $this->getDefaultEnglishTranslation($this, 'title') ?? $this->getName();
    }

    public function getTranslatedName(): ?string
    {
        if ($this->getType() == NakaMenuItemType::TYPE_PAGE && $this->page) {
            return $this->getPage()->getTitle();
        } else {
            return $this->getTitle();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRouteParametersAsArray(): ?array
    {
        $arr =[];
        foreach ($this->routeParameters as $key => $value) {
            $arr[$value['name']] = $value['val'];
        }
        return $arr;
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

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(?string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getRouteParameters(): ?array
    {
        return $this->routeParameters;
    }

    public function setRouteParameters(?array $routeParameters): self
    {
        $this->routeParameters = $routeParameters;

        return $this;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page = null): self
    {
        $this->page = $page;

        return $this;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(?int $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return Collection|Menu[]
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): self
    {
        if (!$this->menus->contains($menu)) {
            $this->menus[] = $menu;
            $menu->addMenuItem($this);
        }

        return $this;
    }

    public function removeMenu(Menu $menu): self
    {
        if ($this->menus->removeElement($menu)) {
            $menu->removeMenuItem($this);
        }

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(?string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
