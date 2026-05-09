<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Validator\BanWordPhp;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 3)]
    #[Assert\Regex(pattern: '/^^[a-zA-ZàâäéèêëîïôöùûüÿçÀÂÄÉÈÊËÎÏÔÖÙÛÜŸÇ ]+$/i')]
    private ?string $name = '';

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 5)]
    #[Assert\Email]
    #[BanWordPhp()]
    private ?string $email = '';

    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column]
    #[Assert\Length(min: 5)]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    #[Assert\Type(Types::BLOB)]
    private mixed $image = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(Types::DATETIME_MUTABLE)]
    private ?\DateTime $bornAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 3)]
    #[Assert\Regex(pattern: '/^^[a-zA-ZàâäéèêëîïôöùûüÿçÀÂÄÉÈÊËÎÏÔÖÙÛÜŸÇ ]+$/i')]
    private ?string $city = '';

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Country]
    private ?string $country = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Type(Types::STRING)]
    #[Assert\Length(max: 1500)]
    private ?string $about = null;

    #[ORM\Column]
    private ?bool $isVerified = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getImage(): mixed
    {
        return $this->image;
    }

    public function setImage(mixed $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getBornAt(): ?\DateTime
    {
        return $this->bornAt;
    }

    public function setBornAt(?\DateTime $bornAt): static
    {
        $this->bornAt = $bornAt;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): static
    {
        $this->about = $about;

        return $this;
    }

    /* OAUTH UTILS */

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // toujours au minimum
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Si tu stockes des données sensibles temporaires, les effacer ici
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
