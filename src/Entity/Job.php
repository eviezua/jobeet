<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\JobRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ApiResource(
        operations: [
            new Get(normalizationContext: ['groups' => 'job:item']),
            new GetCollection(normalizationContext: ['groups' => 'job:list'])
        ]
)]
class Job
{
    public const FULL_TIME_TYPE = 'full-time';
    public const PART_TIME_TYPE = 'part-time';
    public const FREELANCE_TYPE = 'freelance';
    public const TYPES = [
        self::FULL_TIME_TYPE,
        self::PART_TIME_TYPE,
        self::FREELANCE_TYPE,
    ];
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['job:list', 'job:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['job:list', 'job:item'])]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    #[Groups(['job:list', 'job:item'])]
    private ?string $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    #[Groups(['job:list', 'job:item'])]
    private ?string $url = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    #[Groups(['job:list', 'job:item'])]
    private ?string $position = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    #[Groups(['job:list', 'job:item'])]
    private ?string $location = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['job:list', 'job:item'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['job:list', 'job:item'])]
    private ?string $howToApply = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?bool $public = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?bool $activated = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email(  message: 'The email {{ value }} is not a valid email.')]
    #[Groups(['job:list', 'job:item'])]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['job:list', 'job:item'])]
    private ?\DateTimeInterface $expiresAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'jobs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['job:list', 'job:item'])]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getHowToApply(): ?string
    {
        return $this->howToApply;
    }

    public function setHowToApply(string $howToApply): static
    {
        $this->howToApply = $howToApply;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }
    #[ORM\PrePersist]
    public function setToken(): void
    {
        $token = bin2hex(\random_bytes(10));
        $this->token = $token;
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): static
    {
        $this->public = $public;

        return $this;
    }

    public function isActivated(): ?bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): static
    {
        $this->activated = $activated;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeInterface $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
    #[ORM\PrePersist]
    public function updateCreatedAt()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();

        if (!$this->expiresAt) {
            $this->expiresAt = (clone $this->createdAt)->modify('+30 days');
        }
    }
}
