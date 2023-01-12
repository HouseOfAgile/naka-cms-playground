<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

#[ORM\MappedSuperclass]
class MenuItemTranslation implements TranslationInterface
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected $title;

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

}
