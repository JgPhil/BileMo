<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\SerializationContext;




/**
 * @OA\Schema()
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 * @UniqueEntity("name")
 * 
 * @Serializer\ExclusionPolicy("ALL")
 */
class Phone
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min=2, minMessage="Your name must be at least {{ min }} characters long", max=255, maxMessage="Your first name cannot be longer than {{ max }} characters ")
     * @Serializer\Expose
     */
    private $name;

    /**
     * @var string
     * @OA\Property(type="string")
     * @Groups("phone_test")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    private $description;


    /**
     * @var int
     * @OA\Property(type="integer")
     * @ORM\Column(type="string", length=255)
     * @Assert\Positive
     * @Assert\NotBlank
     * @Assert\Range(min=1, minMessage="The minimum value accepted is {{ min }}", max=1500, maxMessage="The maximum value accepted is {{ max }}")
     * @Serializer\Expose
     */
    private $price;

    /**
     * @var string
     * @OA\Property(type="string")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    private $color;

    /**
     * @var \DateTimeInterface
     * @OA\Property(type="string", format="date-time")
     * @ORM\Column(type="datetime")
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s.uT'>")
     * @Serializer\Expose
     */
    private $releasedAt;

    /**
     * @OA\Property(type="object")
     * @ORM\ManyToMany(targetEntity=Customer::class, mappedBy="phones")
     */
    private $customers;


    public function __construct()
    {
        $this->releasedAt = new DateTime();
        $this->customers = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }


    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getReleasedAt(): ?\DateTimeInterface
    {
        return $this->releasedAt;
    }

    public function setReleasedAt($releasedAt): self
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
            $customer->addPhone($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->contains($customer)) {
            $this->customers->removeElement($customer);
            $customer->removePhone($this);
        }

        return $this;
    }
}

