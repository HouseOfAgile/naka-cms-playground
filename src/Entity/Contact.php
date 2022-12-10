<?php

namespace HouseOfAgile\NakaCMSBundle\Entity;

use HouseOfAgile\NakaCMSBundle\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(type: 'string', length: 60)]
    protected $name;

    #[Assert\Email]
    #[Assert\Length(max: 255)]
    #[Assert\NotNull]
    #[ORM\Column(type: 'string', length: 255)]
    protected $email;

    #[ORM\Column(type: 'string', length: 255)]
    protected $subject;

    #[ORM\Column(type: 'text')]
    protected $message;



    public function getId(): ?int
    {
        return $this->id;
    }


    public function __toString()
    {
        return sprintf(
            'Contact #%s %s',
            $this->id,
            $this->getEmail()
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
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'subject' => $this->getSubject(),
            'message' => $this->getMessage(),
        ];

        return $config;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
