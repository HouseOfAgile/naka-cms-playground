<?php

namespace HouseOfAgile\NakaCMSBundle\Entity\AppTrait;

use Doctrine\ORM\Mapping as ORM;

trait BaseEventTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(type: 'integer')]
    private $eventId;

    #[ORM\Column(type: 'datetimetz')]
    private $startDate;

    #[ORM\Column(type: 'datetimetz')]
    private $endDate;

    public function __construct()
    {
        $this->eventId=rand(10000,99999);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventId(): ?int
    {
        return $this->eventId;
    }

    public function setEventId(int $eventId): self
    {
        $this->eventId = $eventId;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }
}
