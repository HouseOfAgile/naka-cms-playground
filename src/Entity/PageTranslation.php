<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use HouseOfAgile\NakaCMSBundle\Repository\PageRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\MappedSuperclass
 */
class PageTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    public function __construct()
    {
        $this->locale = 'en';
    }

    public function __toString()
    {
        return sprintf(
            'PageTranslation #%s %s',
            $this->id,
            substr($this->getTitle(), 0, 39)
        );
    }

    public function getId(): ?int
    {
        return $this->id;
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



    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }
}
