<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\String\Slugger\SluggerInterface;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueEntity('slug')]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'category:item']),
        new GetCollection(normalizationContext: ['groups' => 'category:list'])
    ]
)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category:list', 'category:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['category:list', 'category:item'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Job::class)]
    #[Groups(['category:item'])]
    private Collection $jobs;

    #[ORM\ManyToMany(targetEntity: Affiliate::class, mappedBy: 'categories')]
    private Collection $affilities;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
        $this->affilities = new ArrayCollection();
    }
    public function __toString(): string
    {
        return $this->name;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }
    public function computeSlug(SluggerInterface $slugger)
    {
        if (!$this->slug || '-' === $this->slug) {
            $this->slug = (string) $slugger->slug((string) $this)->lower();
        }
    }
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): static
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs->add($job);
            $job->setCategory($this);
        }

        return $this;
    }

    public function removeJob(Job $job): static
    {
        if ($this->jobs->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getCategory() === $this) {
                $job->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Affiliate>
     */
    public function getAffilities(): Collection
    {
        return $this->affilities;
    }

    public function addAffility(Affiliate $affility): static
    {
        if (!$this->affilities->contains($affility)) {
            $this->affilities->add($affility);
            $affility->addCategory($this);
        }

        return $this;
    }

    public function removeAffility(Affiliate $affility): static
    {
        if ($this->affilities->removeElement($affility)) {
            $affility->removeCategory($this);
        }

        return $this;
    }
    public function getActiveJobs()
    {
        return $this->jobs->filter(function(Job $job) {
            return $job->getExpiresAt() > new \DateTime() && $job->isActivated();
        });
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
