<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="users")
     */
    private $role;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->role ? [$this->role->getName()] : ['ROLE_USER'];
    }

    /**
     * Get the name of the user's role.
     * 
     * @return string|null
     */
    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Permet de nettoyer les données sensibles de l'utilisateur.
     */
    public function eraseCredentials()
    {
        // Si vous stockez des données sensibles temporaires, effacez-les ici
    }

    /**
     * Retourne le nom d'utilisateur utilisé pour l'authentification.
     */
    public function getUsername(): string
    {
        // Remplacez 'mail' par le champ que vous utilisez comme identifiant d'utilisateur
        return $this->mail;
    }
}