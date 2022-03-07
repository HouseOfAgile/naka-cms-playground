<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Entity\PageBlockElement;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Entity\AppTrait\DefaultTranslatableTrait;
use HouseOfAgile\NakaCMSBundle\Repository\StaticPageRepository;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\MappedSuperclass(repositoryClass=StaticPageRepository::class)
 * @UniqueEntity(
 *      fields={"name"},
 *      message="Static page need to have a unique name."
 * )
 */
class StaticPage implements TranslatableInterface, SluggableInterface
{
    use TranslatableTrait;
    use SluggableTrait;
    use DefaultTranslatableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=PageBlockElement::class, mappedBy="staticPage")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $pageBlockElements;

    public function __construct()
    {
        $this->pageBlockElements = new ArrayCollection();
    }


    public function __toString()
    {
        return sprintf(
            'StaticPage #%s %s',
            $this->id,
            substr($this->getTitle(), 0, 39)
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
            'staticPageTranslations' => array_map(function ($pt) {
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
        return ['title'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->getDefaultEnglishTranslation($this, 'title');
    }

    public function getContent()
    {
        return $this->getDefaultEnglishTranslation($this, 'content');
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
            $pageBlockElement->setStaticPage($this);
        }

        return $this;
    }

    public function removePageBlockElement(PageBlockElement $pageBlockElement): self
    {
        if ($this->pageBlockElements->removeElement($pageBlockElement)) {
            // set the owning side to null (unless already changed)
            if ($pageBlockElement->getStaticPage() === $this) {
                $pageBlockElement->setStaticPage(null);
            }
        }

        return $this;
    }
}
