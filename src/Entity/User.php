<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_user', type: 'integer')]
    #[Groups(['user:read'])]
    private ?int $idUser = null;

    #[ORM\Column(name: 'nom', type: 'string', length: 100)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $nom = null;

    #[ORM\Column(name: 'prenom', type: 'string', length: 100, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $prenom = null;

    #[ORM\Column(name: 'email', type: 'string', length: 150, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\Column(name: 'password', type: 'string', length: 255)]
    private ?string $password = null;

    #[ORM\Column(name: 'telephone', type: 'string', length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(name: 'adresse', type: 'string', length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(name: 'ville', type: 'string', length: 100, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(name: 'photo_profil', type: 'string', length: 255, nullable: true)]
    private ?string $photoProfil = null;

    #[ORM\Column(name: 'role', type: 'string', columnDefinition: "ENUM('ADMINISTRATEUR','AGRICULTEUR','CLIENT')")]
    private ?string $role = null;

    #[ORM\Column(name: 'actif', type: 'boolean', options: ['default' => true])]
    private bool $actif = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $dateCreation = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
    }

    // Getters / Setters (simplifiés)
    public function getIdUser(): ?int { return $this->idUser; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(?string $prenom): self { $this->prenom = $prenom; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }
    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): self { $this->telephone = $telephone; return $this; }
    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(?string $adresse): self { $this->adresse = $adresse; return $this; }
    public function getVille(): ?string { return $this->ville; }
    public function setVille(?string $ville): self { $this->ville = $ville; return $this; }
    public function getPhotoProfil(): ?string { return $this->photoProfil; }
    public function setPhotoProfil(?string $photoProfil): self { $this->photoProfil = $photoProfil; return $this; }
    public function getRole(): ?string { return $this->role; }
    public function setRole(string $role): self { $this->role = $role; return $this; }
    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): self { $this->actif = $actif; return $this; }
    public function getDateCreation(): ?\DateTimeInterface { return $this->dateCreation; }
    public function setDateCreation(\DateTimeInterface $dateCreation): self { $this->dateCreation = $dateCreation; return $this; }


    // UserInterface
    public function getUserIdentifier(): string { return $this->email; }
    public function getRoles(): array {
        return match ($this->role) {
            'ADMINISTRATEUR' => ['ROLE_ADMIN'],
            default => ['ROLE_USER'],
        };
    }
    public function eraseCredentials(): void {}
}