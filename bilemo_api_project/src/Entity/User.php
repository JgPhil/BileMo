<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\SerializationContext;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 */
class User
{
    /**
     * @var int
     * @OA\Property(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"detail", "list"})
     */
    private $id;


    /**
     * @var string
     * @OA\Property(type="string")
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"detail", "list"})
     */
    private $firstName;

    /**
     * @var string
     * @OA\Property(type="string")
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"detail", "list"})
     */
    private $lastName;

    /**
     * @var string
     * @OA\Property(type="string")
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"detail", "list"})
     */
    private $email;

    /**
     * @var \DateTimeInterface
     * @OA\Property(type="string", format="date-time")
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"detail", "list"})
     */
    private $createdAt;

    /**
     * @OA\Property(type="object")
     * @var Customer
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"detail"})
     */
    private $customer;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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

    public function setCreatedAt($createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
