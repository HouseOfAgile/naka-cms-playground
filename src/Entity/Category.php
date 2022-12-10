<?php

declare(strict_types=1);

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Entity\AppTrait\DefaultTranslatableTrait;
use HouseOfAgile\NakaCMSBundle\Repository\CategoryRepository;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TreeNodeInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Knp\DoctrineBehaviors\Model\Tree\TreeNodeTrait;

#[ORM\MappedSuperclass(repositoryClass: CategoryRepository::class)]
class Category implements TranslatableInterface, TreeNodeInterface
{
    use TreeNodeTrait;
    use TranslatableTrait;
    // use SluggableTrait;
    use DefaultTranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\ManyToMany(targetEntity: Page::class, mappedBy: 'category')]
    protected $pages;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function __toString(): string
    {
        return (string) 'Category ' . $this->id;
    }

    public function getName()
    {
        return $this->getDefaultEnglishTranslation($this, 'name');
    }

    /**
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['id'];
    }

    /**
     * @return Collection|Page[]
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->addCategory($this);
        }

        return $this;
    }

    public function removePage(Page $page): self
    {
        if ($this->pages->removeElement($page)) {
            $page->removeCategory($this);
        }

        return $this;
    }
}
