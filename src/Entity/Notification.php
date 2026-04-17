<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\Table(name: 'notifications')]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_notif', type: 'integer')]
    private ?int $idNotif = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    private ?User $user = null;

    #[ORM\Column(name: 'message', type: 'string', length: 500)]
    private ?string $message = null;

    #[ORM\Column(name: 'type', type: 'string', length: 20)]
    private ?string $type = null;

    #[ORM\Column(name: 'lu', type: 'boolean', options: ['default' => false])]
    private bool $lu = false;

    #[ORM\Column(name: 'date_notif', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $dateNotif = null;

    // Getters/Setters
    public function getIdNotif(): ?int { return $this->idNotif; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
    public function getMessage(): ?string { return $this->message; }
    public function setMessage(string $message): self { $this->message = $message; return $this; }
    public function getType(): ?string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function isLu(): bool { return $this->lu; }
    public function setLu(bool $lu): self { $this->lu = $lu; return $this; }
    public function getDateNotif(): ?\DateTimeInterface { return $this->dateNotif; }
    public function setDateNotif(\DateTimeInterface $dateNotif): self { $this->dateNotif = $dateNotif; return $this; }
}