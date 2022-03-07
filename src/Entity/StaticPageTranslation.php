<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\MappedSuperclass
 */
class StaticPageTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    public function __construct()
    {
        $this->locale = 'en';
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
            'content' => $this->getContent(),
            'locale' => $this->getLocale(),
        ];

        return $config;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
