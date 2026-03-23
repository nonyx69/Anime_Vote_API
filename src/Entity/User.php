<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:sign'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:sign'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:sign'])]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[Groups(['user:sign'])]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['user:sign'])]
    #[ORM\Column]
    private array $roles = [];

    #[Groups(['user:sign'])]
    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Profil $profil = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }
    public function getRoles_admin(): array
    {

        return $this->roles;

    }

    public function getRole(): array
    {

        return $this->roles;

    }

    public function getRoles(): array
    {

        return $this->roles;
        $this->roles[] = "ROLE_USER";

        return array_unique($roles);

    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

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

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(Profil $profil): static
    {
        if ($profil->getUser() !== $this) {
            $profil->setUser($this);
        }

        $this->profil = $profil;
        return $this;
    }
}
