<?php

namespace App\Entity;

use App\Repository\ActiveMinigameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActiveMinigameRepository::class)]
class ActiveMinigame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, unique: true)]
    private ?GameSession $session = null;

    #[ORM\Column(length: 50)]
    private ?string $minigameType = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GamePlayer $challenger = null;

    #[ORM\ManyToOne]
    private ?GamePlayer $opponent = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $scannedAt = null;

    #[ORM\Column(nullable: true)]
    private ?array $caseData = null;

    public function __construct()
    {
        $this->scannedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMinigameType(): ?string
    {
        return $this->minigameType;
    }

    public function setMinigameType(string $minigameType): static
    {
        $this->minigameType = $minigameType;

        return $this;
    }

    public function getChallenger(): ?GamePlayer
    {
        return $this->challenger;
    }

    public function setChallenger(?GamePlayer $challenger): static
    {
        $this->challenger = $challenger;

        return $this;
    }

    public function getOpponent(): ?GamePlayer
    {
        return $this->opponent;
    }

    public function setOpponent(?GamePlayer $opponent): static
    {
        $this->opponent = $opponent;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getScannedAt(): ?\DateTimeImmutable
    {
        return $this->scannedAt;
    }

    public function setScannedAt(\DateTimeImmutable $scannedAt): static
    {
        $this->scannedAt = $scannedAt;

        return $this;
    }

    public function getCaseData(): ?array
    {
        return $this->caseData;
    }

    public function setCaseData(?array $caseData): static
    {
        $this->caseData = $caseData;

        return $this;
    }
}
