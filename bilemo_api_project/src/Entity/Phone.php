<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 */
class Phone
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list", "show"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list", "show"})
     * @Assert\Length(min="2", max="255")
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("show")
     * @Assert\NotBlank
     */
    private $description;


    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list", "show"})
     * @Assert\Range(min="0", max="1500")
     * @Assert\NotBlank
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("show")
     * @Assert\NotBlank
     */
    private $color;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("show")
     */
    private $releasedAt;


    public function __construct()
    {
        $this->releasedAt = new DateTime();
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

    public function setReleasedAt(\DateTimeInterface $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }
}

