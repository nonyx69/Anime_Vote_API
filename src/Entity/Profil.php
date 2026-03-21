<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['user:sign'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_profil = null;

    #[Groups(['user:sign'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'profil')]
    #[ORM\JoinColumn(nullable: true)]
    private ?user $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageProfil(): ?string
    {
        return $this->image_profil;
    }

    public function setImageProfil(?string $image_profil): static
    {
        $this->image_profil = $image_profil;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(user $user): static
    {
        $this->user = $user;

        return $this;
    }
}
