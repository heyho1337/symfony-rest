<?php

namespace App\Entity;

use App\Repository\TeamsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamsRepository::class)]
class Teams
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $teams_id = null;

    #[ORM\Column(length: 50)]
    private ?string $account_id = null;

    #[ORM\Column(length: 50)]
    private ?string $league_id = null;

    #[ORM\Column(length: 50)]
    private ?string $user_email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeamsId(): ?string
    {
        return $this->teams_id;
    }

    public function setTeamsId(string $teams_id): static
    {
        $this->teams_id = $teams_id;

        return $this;
    }

    public function getAccountId(): ?string
    {
        return $this->account_id;
    }

    public function setAccountId(string $account_id): static
    {
        $this->account_id = $account_id;

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

    public function getUserEmail(): ?string
    {
        return $this->user_email;
    }

    public function setUserEmail(string $user_email): static
    {
        $this->user_email = $user_email;

        return $this;
    }
}
