<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="`user`")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 */
abstract class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetClassStudents"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetClassStudents"})
     */
    protected $username;

    /**
     * @ORM\Column(type="json")
     * @Serializer\Exclude()
     */
    protected $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Serializer\Exclude()
     */
    protected $password;

    /**
     * @var string
     * @Serializer\Exclude()
     */
    protected $plainPassword;


    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"Default", "Deserialization"})
     */
    protected $email;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Groups({"Default", "Deserialization"})
     */
    private $lastLogin;

    /**
     * @ORM\Column(type="string", length=100)
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetClassStudents"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=100)
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetClassStudents"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $devices = [];

    public function getType() {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return \array_search($role, $this->roles) !== false;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
         $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getDevices(): ?array
    {
        return $this->devices;
    }

    public function setDevices(?array $devices): self
    {
        $this->devices = $devices;

        return $this;
    }

    public function addDevice(string $deviceId): self
    {
        $key = array_search($deviceId, $this->devices);
        if ($key === false) {
            $this->devices[] = $deviceId;
        }

        return $this;
    }

    public function deleteDevice(string $deviceId): self
    {
        $key = array_search($deviceId, $this->devices);
        if ($key !== null) {
            unset($this->devices[$key]);
        }

        return $this;
    }
}
