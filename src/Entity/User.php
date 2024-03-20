<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $user_name = null;

    #[ORM\Column(length: 50)]
    private ?string $user_email = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $active_league = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $leagueId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->user_name;
    }

    public function setUserName(string $user_name): static
    {
        $this->user_name = $user_name;

        return $this;
    }

    public function getUserEmail(): ?string
    {
        return $this->user_email;
    }

    public function setUserEmail(string $user_email): static
    {
        $this->user_email = $user_email;

        return $this;
    }

    public function getActiveLeague(): ?string
    {
        return $this->active_league;
    }

    public function setActiveLeague(?string $active_league): static
    {
        $this->active_league = $active_league;

        return $this;
    }

    public function getLeagueId(): ?string
    {
        return $this->leagueId;
    }

    public function setLeagueId(?string $leagueId): static
    {
        $this->leagueId = $leagueId;

        return $this;
    }
}
