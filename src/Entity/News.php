<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
#[ORM\UniqueConstraint(columns: ['published_by', 'published_id'])]
#[ORM\Index(columns: ['updated_at'])]
#[ORM\Index(columns: ['title', 'summary', 'published_by'], flags: ['fulltext'])]
#[ORM\HasLifecycleCallbacks]
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 512)]
    private ?string $summary = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    private ?string $published_by = null;

    #[ORM\Column(length: 255)]
    private ?string $published_id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $published_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return $this
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @return $this
     */
    public function setSummary(string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return $this
     */
    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishedBy(): ?string
    {
        return $this->published_by;
    }

    /**
     * @return $this
     */
    public function setPublishedBy(string $published_by): static
    {
        $this->published_by = $published_by;

        return $this;
    }

    public function getPublishedId(): ?string
    {
        return $this->published_id;
    }

    /**
     * @return $this
     */
    public function setPublishedId(string $published_id): static
    {
        $this->published_id = $published_id;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->published_at;
    }

    /**
     * @return $this
     */
    public function setPublishedAt(\DateTimeImmutable $published_at): static
    {
        $this->published_at = $published_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * @return $this
     */
    #[ORM\PrePersist]
    public function setUpdatedAt(): static
    {
        $this->updated_at = new \DateTimeImmutable();

        return $this;
    }
}
