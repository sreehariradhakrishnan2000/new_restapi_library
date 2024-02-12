<?php

// src/Entity/User.php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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
        // Doctrine automatically converts JSON string to array, so just return the property
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        // Since roles are stored as JSON in the database, we need to encode them before setting
        $this->roles = json_encode($roles);

        return $this;
    }

    public function getSalt()
    {
        // Not needed when using bcrypt or argon2i
    }

    public function eraseCredentials()
    {
        // Remove sensitive data stored on the user, like plaintext passwords
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}
