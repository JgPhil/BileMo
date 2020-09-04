<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\SerializationContext;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @OA\Schema()
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * 
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "show_customer",
 *          parameters = { "username" = "expr(object.getUsername())" },
 *          absolute=true
 *      )
 * )
 * 
 * @Hateoas\Relation(
 *      "list_users",
 *      href = @Hateoas\Route(
 *          "list_user",
 *          absolute=true
 *      )
 * )
 * 
 * @Serializer\ExclusionPolicy("ALL")
 * 
 */
class Customer implements UserInterface
{
    /**
     * @var int
     * @OA\Property(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * 
     * @Serializer\Expose
     */
    private $id;

    /**
     * @var string
     * @OA\Property(type="string")
     * @ORM\Column(type="string", length=255)
     * 
     * @Serializer\Expose
     */
    private $username;

    /**
     * @var string
     * @OA\Property(type="string")
     * @ORM\Column(type="string", length=255)
     * 
     * @Serializer\Expose
     */
    private $email;

    /**
     * @var \DateTimeInterface
     * @OA\Property(type="string", format="date-time")
     * @ORM\Column(type="datetime")
     * 
     * @Serializer\Expose
     */
    private $createdAt;

    /**
     * @OA\Property(type="object")
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="customer", orphanRemoval=true)
     * 
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    private $salt;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }


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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCustomer($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getCustomer() === $this) {
                $user->setCustomer(null);
            }
        }

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
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): array
    {
        $this->roles = $roles;
        return $this->roles;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function eraseCredentials()
    {
    }
}
