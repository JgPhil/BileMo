<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\SerializationContext;
use OpenApi\Annotations as OA;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @OA\Schema()
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 * 
 * 
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "show_user",
 *          parameters = { 
 *              "id" = "expr(object.getId())",
 *              "username" = "expr(object.getCustomer().getUsername())"
*           },
 *          absolute=true
 *      )
 * )
 * 
 * @Hateoas\Relation(
 *      "add_user",
 *      href = @Hateoas\Route(
 *          "add_user",
 *          parameters = { 
 *              "username" = "expr(object.getCustomer().getUsername())"
*           },
 *          absolute=true
 *      )
 * )
 * 
 * @Hateoas\Relation(
 *      "update_user",
 *      href = @Hateoas\Route(
 *          "update_user",
 *          parameters = { 
 *              "id" = "expr(object.getId())",
 *              "username" = "expr(object.getCustomer().getUsername())",
 *           },
 *          absolute=true
 *      )
 * )
 * 
 * @Hateoas\Relation(
 *      "delete_user",
 *      href = @Hateoas\Route(
 *          "delete_user",
 *          parameters = { 
 *              "id" = "expr(object.getId())",
 *              "username" = "expr(object.getCustomer().getUsername())",
 *           },
 *          absolute=true
 *      )
 * )
 * 
 * @Hateoas\Relation(
 *      "customer",
 *      href = @Hateoas\Route(
 *          "show_customer",
 *          parameters = { "username" = "expr(object.getCustomer().getUsername())" },
 *          absolute=true
 *      )
 * )
 * 
 * @Serializer\ExclusionPolicy("ALL")
 * 
 */
class User
{
    /**
     * @var int
     * @OA\Property(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    private $id;


    /**
     * @var string
     * @OA\Property(type="string")
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     * 
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     */
    private $firstName;

    /**
     * @var string
     * @OA\Property(type="string")
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     * 
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     */
    private $lastName;

    /**
     * @var string
     * @OA\Property(type="string")
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     * @Assert\Email()
     */
    private $email;

    /**
     * @var \DateTimeInterface
     * @OA\Property(type="string", format="date-time")
     * @ORM\Column(type="datetime")
     * @Serializer\Expose
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s.uT'>")
     */
    private $createdAt;

    /**
     * @OA\Property(type="object")
     * @var Customer
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
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
