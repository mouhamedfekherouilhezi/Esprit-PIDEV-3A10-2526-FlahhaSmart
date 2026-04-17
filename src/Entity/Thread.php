<?php

namespace App\Entity;

use App\Repository\ThreadRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ThreadRepository::class)]
#[ORM\Table(name: 'threads')]
class Thread
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_thread', type: 'integer')]
    #[Groups(['thread:read'])]
    private ?int $idThread = null;

    #[ORM\Column(name: 'titre', type: 'string', length: 255)]
    #[Groups(['thread:read', 'thread:write'])]
    private ?string $titre = null;

    #[ORM\Column(name: 'contenu', type: 'text')]
    #[Groups(['thread:read', 'thread:write'])]
    private ?string $contenu = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    #[Groups(['thread:read'])]
    private ?User $auteur = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['thread:read'])]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(name: 'date_update', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP', 'onUpdate' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $dateUpdate = null;

    #[ORM\Column(name: 'statut', type: 'string', length: 10, options: ['default' => 'actif'])]
    private ?string $statut = 'actif';

    #[ORM\Column(name: 'sentiment', type: 'string', length: 10, nullable: true)]
    private ?string $sentiment = null;

    #[ORM\Column(name: 'tags', type: 'string', length: 500, nullable: true)]
    #[Groups(['thread:read', 'thread:write'])]
    private ?string $tags = null;

    /**
     * Propriétés virtuelles pour l'affichage (non persistées)
     */
    public int $score = 0;
    public ?string $userVote = null;
    public int $likesCount = 0;
    public bool $liked = false;

    // Getters / Setters

    public function getIdThread(): ?int { return $this->idThread; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }

    public function getContenu(): ?string { return $this->contenu; }
    public function setContenu(string $contenu): self { $this->contenu = $contenu; return $this; }

    public function getAuteur(): ?User { return $this->auteur; }
    public function setAuteur(?User $auteur): self { $this->auteur = $auteur; return $this; }

    public function getDateCreation(): ?\DateTimeInterface { return $this->dateCreation; }
    public function setDateCreation(\DateTimeInterface $dateCreation): self { $this->dateCreation = $dateCreation; return $this; }


    public function getDateUpdate(): ?\DateTimeInterface { return $this->dateUpdate; }
    public function setDateUpdate(\DateTimeInterface $dateUpdate): self { $this->dateUpdate = $dateUpdate; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }

    public function getSentiment(): ?string { return $this->sentiment; }
    public function setSentiment(?string $sentiment): self { $this->sentiment = $sentiment; return $this; }

    public function getTags(): ?string { return $this->tags; }
    public function setTags(?string $tags): self { $this->tags = $tags; return $this; }
}