<?php

namespace App\Entity;

use App\Repository\JaimeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JaimeRepository::class)]
#[ORM\Table(name: 'jaime')]
class Jaime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_jaime', type: 'integer')]
    private ?int $idJaime = null;

    #[ORM\ManyToOne(targetEntity: Thread::class)]
    #[ORM\JoinColumn(name: 'id_thread', referencedColumnName: 'id_thread')]
    private ?Thread $thread = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    private ?User $user = null;

    #[ORM\Column(name: 'date_jaime', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $dateJaime = null;

    // Getters / Setters

    public function getIdJaime(): ?int { return $this->idJaime; }
    public function getThread(): ?Thread { return $this->thread; }
    public function setThread(?Thread $thread): self { $this->thread = $thread; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
    public function getDateJaime(): ?\DateTimeInterface { return $this->dateJaime; }
    public function setDateJaime(\DateTimeInterface $dateJaime): self { $this->dateJaime = $dateJaime; return $this; }
}