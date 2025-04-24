<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

#[ORM\MappedSuperclass]
class PictureTranslation implements TranslationInterface
{
	use TranslationTrait;

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'AUTO')]
	#[ORM\Column(type: 'integer')]
	private $id;

	#[ORM\Column(type: 'string', length: 255, nullable: true)]
	private $altText;


	public function __construct()
	{
		$this->locale = 'en';
	}
	public function getId(): ?int
	{
		return $this->id;
	}
	public function __toString()
	{
		return sprintf(
			'PictureTranslation #%s %s',
			$this->id,
			$this->getAltText() !== null ? substr($this->getAltText(), 0, 39) : '(No Title)'
		);
	}

	public function getAltText(): ?string
	{
		return $this->altText;
	}

	public function setAltText(?string $altText): self
	{
		$this->altText = $altText;
		return $this;
	}
}
