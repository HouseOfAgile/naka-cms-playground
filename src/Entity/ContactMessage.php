<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use App\Entity\ContactThread;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Repository\ContactMessageRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass(repositoryClass: ContactMessageRepository::class)]
class ContactMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ContactThread::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?ContactThread $thread = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    protected ?string $message = null;

    #[ORM\Column(type: 'datetime_immutable')]
    protected \DateTimeImmutable $createdAt;

    // If "true", this message was posted by an Admin from the backend
    #[ORM\Column(type: 'boolean')]
    protected bool $isFromAdmin = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getThread(): ?ContactThread
    {
        return $this->thread;
    }

    public function setThread(?ContactThread $thread): self
    {
        $this->thread = $thread;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function isFromAdmin(): bool
    {
        return $this->isFromAdmin;
    }

    public function setIsFromAdmin(bool $isFromAdmin): self
    {
        $this->isFromAdmin = $isFromAdmin;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            'Message #%d (admin=%s)',
            $this->id ?? 0,
            $this->isFromAdmin ? 'yes' : 'no'
        );
    }
}
