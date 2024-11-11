<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use App\Util\ConversionUtil;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="notes")
     */
    private ?Category $category;

    /**
     * @ORM\ManyToOne(targetEntity=Prompt::class, inversedBy="notes")
     */
    private ?Prompt $prompt;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, mappedBy="notes")
     */
    private Collection $tags;

    /**
     * @ORM\ManyToOne(targetEntity=Folder::class, inversedBy="notes")
     */
    private ?Folder $folder;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('NOW');
        $this->updatedAt = new \DateTime('NOW');

        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * only for testing
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function addContent(string $content): self
    {
        $this->content .= $content;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPrompt(): ?Prompt
    {
        return $this->prompt;
    }

    public function setPrompt(?Prompt $prompt): self
    {
        $this->prompt = $prompt;

        return $this;
    }

    public function getTags(): ArrayCollection
    {
        return ConversionUtil::convertCollectionIntoArrayCollection($this->tags);
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addNote($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeNote($this);
        }

        return $this;
    }

    public function getFolder(): ?Folder
    {
        return $this->folder;
    }

    public function setFolder(?Folder $folder): self
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * @param bool $withContent
     * @param bool $withCategory
     * @param bool $withPrompt
     * @param bool $withTags
     * @param bool $withFolder
     * @return array
     */
    public function jsonSerialize(bool $withContent = true, bool $withCategory = true, bool $withPrompt = true, bool $withTags = true, bool $withFolder = true): array
    {
        $json = [
            'id' => $this->id,
            'title' => $this->title,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];

        if ($withContent) {
            $json['content'] = $this->content;
        }

        if ($withCategory) {
            $json['category'] = $this->category->jsonSerialize();
        }

        if ($withPrompt) {
            $json['prompt'] = $this->prompt->jsonSerialize();
        }

        if ($withTags) {
            foreach ($this->getTags() as $tag) {
                $json += ['Tag with id: ' . $tag->getId() => $tag->jsonSerialize()];
            }
        }

        if ($withFolder) {
            $json['folder'] = $this->folder->jsonSerialize();
        }
        return $json;
    }
}
