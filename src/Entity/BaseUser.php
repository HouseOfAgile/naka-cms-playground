<?php
namespace HouseOfAgile\NakaCMSBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use HouseOfAgile\NakaCMSBundle\Entity\AppTrait\AddressTrait;
use HouseOfAgile\NakaCMSBundle\Repository\UserRepository;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[ORM\MappedSuperclass(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class BaseUser implements UserInterface, PasswordAuthenticatedUserInterface, TimestampableInterface
{
    use TimestampableTrait;
    use AddressTrait;

    const DEFAULT_PREFERRED_LOCALE = 'en';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    protected $email;

    #[ORM\Column(type: 'json')]
    protected $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    protected $password;

    #[ORM\Column(type: 'string', length: 255)]
    protected $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    protected $lastName;

    #[Assert\PasswordStrength([
            'minScore' => PasswordStrength::STRENGTH_WEAK,
        ])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected $plainPassword;

    #[ORM\Column(length: 5, nullable: true)]
    protected ?string $preferredLocale = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $birthDate = null;

    #[ORM\Column]
    protected ?bool $isVerified = false;

    #[ORM\Column(type : 'datetime', nullable: true)]
    protected ?\DateTimeInterface $lastVerificationEmailSentAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return sprintf('%s [#%s: %s]', $this->getFullName(),
            $this->getId(),
            $this->getEmail(),
        );
    }

    public function getFullName($lastNameFirst = true): string
    {
        return sprintf(
            '%s %s',
            $lastNameFirst ? $this->getLastName() : $this->getFirstName(),
            $lastNameFirst ? $this->getFirstName() : $this->getLastName(),
        );
    }
    public function getHumanName(): string
    {
        return sprintf('%s [%s]', $this->getFullName(), $this->getEmail());
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->getUsername();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): ?string
    {
        return (string) $this->getEmail();
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole($role): self
    {
        if (! in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return null;
    }

    /**
     * @see UserInterface
     * @return void
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate( ? \DateTimeInterface $birthDate) : self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isIsVerified();
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getPreferredLocale(): ?string
    {
        return $this->preferredLocale ?? self::DEFAULT_PREFERRED_LOCALE;
    }

    public function setPreferredLocale(?string $preferredLocale): static
    {
        $this->preferredLocale = $preferredLocale;

        return $this;
    }

    public function getLastVerificationEmailSentAt(): ?\DateTimeInterface
    {
        return $this->lastVerificationEmailSentAt;
    }

    public function setLastVerificationEmailSentAt( ? \DateTimeInterface $lastVerificationEmailSentAt) : self
    {
        $this->lastVerificationEmailSentAt = $lastVerificationEmailSentAt;
        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
