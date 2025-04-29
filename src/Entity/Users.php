<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "user_id", type: "integer")]
    private ?int $userId = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $lastName = null;

    #[ORM\Column(name: "email", length: 50, unique: true)]
    private ?string $email = null;

    #[ORM\Column(name: "password", length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 15)]
    private ?string $phoneNum = null;

    #[ORM\Column(length: 150)]
    private ?string $address = null;

    #[ORM\Column(options: ["default" => 0])]
    private ?int $role = 0;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $photo = null;

    #[ORM\Column(options: ["default" => 0])]
    private ?int $compte = 0;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $notifications;

    #[ORM\OneToMany(targetEntity: ListeFavoris::class, mappedBy: 'user')]
    private Collection $favoris;

    #[ORM\ManyToMany(targetEntity: Excursions::class, inversedBy: "users")]
    #[ORM\JoinTable(
        name: "users_excursions",
        joinColumns: [new ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "excursion_id", referencedColumnName: "excursion_id")]
    )]
    private Collection $excursions;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->excursions = new ArrayCollection();
        $this->favoris = new ArrayCollection();
    }

    public function getFavoris(): Collection
{
    return $this->favoris;
}
    public function getUserId(): ?int { return $this->userId; }

    public function getName(): ?string { return $this->name; }

    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getLastName(): ?string { return $this->lastName; }

    public function setLastName(string $lastName): static { $this->lastName = $lastName; return $this; }

    public function getEmail(): ?string { return $this->email; }

    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getPassword(): ?string { return $this->password; }

    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function getPhoneNum(): ?string { return $this->phoneNum; }

    public function setPhoneNum(string $phoneNum): static { $this->phoneNum = $phoneNum; return $this; }

    public function getAddress(): ?string { return $this->address; }

    public function setAddress(string $address): static { $this->address = $address; return $this; }

    public function getRole(): ?int { return $this->role; }

    public function setRole(?int $role): static { $this->role = $role; return $this; }

    public function getCompte(): ?int { return $this->compte; }

    public function setCompte(?int $compte): static { $this->compte = $compte; return $this; }

    public function isBlocked(): bool { return $this->compte === 1; }

    public function block(): void { $this->compte = 1; }

    public function unblock(): void { $this->compte = 0; }

    public function getPhoto(): ?string
    {
        if (is_resource($this->photo)) {
            rewind($this->photo);
            return stream_get_contents($this->photo);
        }
        return $this->photo;
    }

    public function setPhoto($photo): static
    {
        if ($photo instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            $this->photo = file_get_contents($photo->getPathname());
        } else {
            $this->photo = $photo;
        }
        return $this;
    }

    public function getPhotoBase64(): ?string
    {
        if (!$this->photo) return null;
        return 'data:image/jpeg;base64,' . base64_encode($this->getPhoto());
    }

    public function getRoles(): array
    {
        return $this->role === 1 ? ['ROLE_ADMIN'] : ['ROLE_USER'];
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials(): void {}

    public function getNotifications(): Collection { return $this->notifications; }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }
        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }
        return $this;
    }

    public function getExcursions(): Collection { return $this->excursions; }

    public function addExcursion(Excursions $excursion): static
    {
        if (!$this->excursions->contains($excursion)) {
            $this->excursions[] = $excursion;
        }
        return $this;
    }

    public function removeExcursion(Excursions $excursion): static
    {
        $this->excursions->removeElement($excursion);
        return $this;
    }
}
