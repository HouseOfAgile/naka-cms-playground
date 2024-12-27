<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\DBAL\Types\Types;
use HouseOfAgile\NakaCMSBundle\Repository\PageRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

#[ORM\MappedSuperclass]
class PageTranslation implements TranslationInterface
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected $title;

    #[ORM\Column(type: 'text', nullable: true)]
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
			$this->getTitle() !== null ? substr($this->getTitle(), 0, 39) : '(No Title)'
        );
    }

    public function getId(): ?int
    {
        return $this->id;
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
