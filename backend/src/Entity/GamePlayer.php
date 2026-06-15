<?php

namespace App\Entity;

use App\Repository\GamePlayerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GamePlayerRepository::class)]
class GamePlayer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $username = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $position = 0;

    #[ORM\Column]
    private ?int $turnOrder = null;

    #[ORM\Column]
    private ?bool $isHost = null;

    #[ORM\Column]
    private ?bool $isReady = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $gameToken = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSession $session = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getTurnOrder(): ?int
    {
        return $this->turnOrder;
    }

    public function setTurnOrder(int $turnOrder): static
    {
        $this->turnOrder = $turnOrder;

        return $this;
    }

    public function isHost(): ?bool
    {
        return $this->isHost;
    }

    public function setIsHost(bool $isHost): static
    {
        $this->isHost = $isHost;

        return $this;
    }

    public function isReady(): ?bool
    {
        return $this->isReady;
    }

    public function setIsReady(bool $isReady): static
    {
        $this->isReady = $isReady;

        return $this;
    }

    public function getGameToken(): ?string
    {
        return $this->gameToken;
    }

    public function setGameToken(string $gameToken): static
    {
        $this->gameToken = $gameToken;

        return $this;
    }

    public function getSession(): ?GameSession
    {
        return $this->session;
    }

    public function setSession(?GameSession $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
