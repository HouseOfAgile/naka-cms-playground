<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\MappedSuperclass
 */
class WebsiteInfoTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $catchPhrase;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return sprintf(
            'StaticPageTranslation #%s %s',
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
            'title' => $this->getTitle(),
            'catchPhrase' => $this->getCatchPhrase(),
            'locale' => $this->getLocale(),
        ];

        return $config;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCatchPhrase(): ?string
    {
        return $this->catchPhrase;
    }

    public function setCatchPhrase(?string $catchPhrase): self
    {
        $this->catchPhrase = $catchPhrase;

        return $this;
    }
}
