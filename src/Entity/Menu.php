<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Entity\MenuItem;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Repository\MenuRepository;

#[ORM\MappedSuperclass(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected $name;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected $isFixed;

    #[ORM\ManyToMany(targetEntity: MenuItem::class, inversedBy: 'menus')]
    #[ORM\OrderBy(['position' => 'ASC'])]
    protected $menuItems;



    public function __construct()
    {
        $this->menuItems = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf(
            'Menu #%s %s',
            $this->id,
            $this->name,
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
            'menuItems' => array_map(function ($mi) {
                return $mi->getId();
            }, $this->getMenuItems()->toArray()),
        ];

        return $config;
    }

    public function getOrderedMenuItemsArray(): array
    {
        return  array_map(
            function ($o) {
                return $o->getId();
            },
            $this->getMenuItems()->toArray()
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

    public function getIsFixed(): ?bool
    {
        return $this->isFixed;
    }

    public function setIsFixed(?bool $isFixed): self
    {
        $this->isFixed = $isFixed;

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
        }

        return $this;
    }

    public function removeMenuItem(MenuItem $menuItem): self
    {
        $this->menuItems->removeElement($menuItem);

        return $this;
    }
}
