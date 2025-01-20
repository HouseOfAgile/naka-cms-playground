<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Config\ContactThreadStatus;
use HouseOfAgile\NakaCMSBundle\Repository\ContactThreadRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass(repositoryClass: ContactThreadRepository::class)]
class ContactThread
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private ?string $subject = null;

    /**
     * This is our enum-based status field.
     * Enums require Doctrine to be >= 2.13 and DBAL >= 3.2 for enumType mapping.
     */
    #[ORM\Column(type: 'string', enumType: ContactThreadStatus::class, length: 20)]
    private ContactThreadStatus $status = ContactThreadStatus::NEW;

    #[ORM\Column(type: 'string', length: 60, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * One thread has many messages (the conversation).
     */
    #[ORM\OneToMany(
        mappedBy: 'thread',
        targetEntity: ContactMessage::class,
        cascade: ['persist', 'remove']
    )]
    private Collection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->status   = ContactThreadStatus::NEW;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getStatus(): ContactThreadStatus
    {
        return $this->status;
    }

    public function setStatus(ContactThreadStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return Collection|ContactMessage[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(ContactMessage $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setThread($this);
        }
        return $this;
    }

    public function removeMessage(ContactMessage $message): self
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getThread() === $this) {
                $message->setThread(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            'Thread #%d: %s (%s)',
            $this->id ?? 0,
            $this->subject,
            $this->status->value
        );
    }
}
