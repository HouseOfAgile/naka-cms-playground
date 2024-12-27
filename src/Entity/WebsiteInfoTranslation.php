<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

#[ORM\MappedSuperclass]
class WebsiteInfoTranslation implements TranslationInterface
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    protected $title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected $catchPhrase;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->locale = 'en';
    }

    public function __toString()
    {
        return sprintf(
            'WebsiteInfoTranslation #%s %s',
            $this->id,
			$this->getTitle() !== null ? substr($this->getTitle(), 0, 39) : '(No Title)'
        );
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
