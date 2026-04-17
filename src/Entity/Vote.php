<?php

namespace App\Entity;

use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
#[ORM\Table(name: 'votes')]
class Vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_vote', type: 'integer')]
    private ?int $idVote = null;

    #[ORM\ManyToOne(targetEntity: Thread::class)]
    #[ORM\JoinColumn(name: 'id_thread', referencedColumnName: 'id_thread')]
    private ?Thread $thread = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    private ?User $user = null;

    #[ORM\Column(name: 'type_vote', type: 'string', length: 5)]
    private ?string $typeVote = null;

    #[ORM\Column(name: 'date_vote', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $dateVote = null;

    // Getters / Setters

    public function getIdVote(): ?int { return $this->idVote; }
    public function getThread(): ?Thread { return $this->thread; }
    public function setThread(?Thread $thread): self { $this->thread = $thread; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
    public function getTypeVote(): ?string { return $this->typeVote; }
    public function setTypeVote(string $typeVote): self { $this->typeVote = $typeVote; return $this; }
    public function getDateVote(): ?\DateTimeInterface { return $this->dateVote; }
    public function setDateVote(\DateTimeInterface $dateVote): self { $this->dateVote = $dateVote; return $this; }
}