<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
#[ORM\Table(name: 'commentaires')]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_commentaire', type: 'integer')]
    #[Groups(['comment:read'])]
    private ?int $idCommentaire = null;

    #[ORM\ManyToOne(targetEntity: Thread::class)]
    #[ORM\JoinColumn(name: 'id_thread', referencedColumnName: 'id_thread')]
    #[Groups(['comment:read'])]
    private ?Thread $thread = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    #[Groups(['comment:read'])]
    private ?User $auteur = null;

    #[ORM\Column(name: 'contenu', type: 'text')]
    #[Groups(['comment:read', 'comment:write'])]
    private ?string $contenu = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['comment:read'])]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(name: 'statut', type: 'string', length: 10, options: ['default' => 'actif'])]
    private ?string $statut = 'actif';

    #[ORM\Column(name: 'sentiment', type: 'string', length: 10, nullable: true)]
    private ?string $sentiment = null;

    // Champs pour l'IA (à ajouter via migration si besoin)
    #[ORM\Column(name: 'modere_ia', type: 'boolean', options: ['default' => false], nullable: true)]
    private ?bool $modereIA = false;

    #[ORM\Column(name: 'flagge', type: 'boolean', options: ['default' => false], nullable: true)]
    private ?bool $flagge = false;

    // Getters / Setters

    public function getIdCommentaire(): ?int { return $this->idCommentaire; }

    public function getThread(): ?Thread { return $this->thread; }
    public function setThread(?Thread $thread): self { $this->thread = $thread; return $this; }

    public function getAuteur(): ?User { return $this->auteur; }
    public function setAuteur(?User $auteur): self { $this->auteur = $auteur; return $this; }

    public function getContenu(): ?string { return $this->contenu; }
    public function setContenu(string $contenu): self { $this->contenu = $contenu; return $this; }

    public function getDateCreation(): ?\DateTimeInterface { return $this->dateCreation; }
    public function setDateCreation(\DateTimeInterface $dateCreation): self { $this->dateCreation = $dateCreation; return $this; }


    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }

    public function getSentiment(): ?string { return $this->sentiment; }
    public function setSentiment(?string $sentiment): self { $this->sentiment = $sentiment; return $this; }

    public function isModereIA(): ?bool { return $this->modereIA; }
    public function setModereIA(?bool $modereIA): self { $this->modereIA = $modereIA; return $this; }

    public function isFlagge(): ?bool { return $this->flagge; }
    public function setFlagge(?bool $flagge): self { $this->flagge = $flagge; return $this; }
}