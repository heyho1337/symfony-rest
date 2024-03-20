<?php

namespace App\Entity;

use App\Repository\LeaguesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeaguesRepository::class)]
class Leagues
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $active = null;

    #[ORM\Column]
    private ?bool $full = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $league_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getFull(): ?bool
    {
        return $this->full;
    }

    public function setFull(bool $full): static
    {
        $this->full = $full;

        return $this;
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

    public function getLeagueId(): ?string
    {
        return $this->league_id;
    }

    public function setLeagueId(string $league_id): static
    {
        $this->league_id = $league_id;

        return $this;
    }
}
