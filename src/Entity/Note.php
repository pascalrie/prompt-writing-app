<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use App\Util\ConversionUtil;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a note entity in the application.
 *
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
{
    /**
     * The unique identifier for the note.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * The title of the note.
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * The content of the note. Optional field.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content = null;

    /**
     * The timestamp when the note was created. Immutable.
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * The timestamp when the note was last updated.
     *
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $updatedAt;

    /**
     * The category to which this note belongs. Optional association.
     *
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="notes")
     */
    private ?Category $category;

    /**
     * The prompt linked to this note. Optional association.
     *
     * @ORM\ManyToOne(targetEntity=Prompt::class, inversedBy="notes")
     */
    private ?Prompt $prompt;

    /**
     * A collection of tags associated with this note.
     *
     * @ORM\ManyToMany(targetEntity=Tag::class, mappedBy="notes")
     */
    private Collection $tags;

    /**
     * The folder containing this note. Optional association.
     *
     * @ORM\ManyToOne(targetEntity=Folder::class, inversedBy="notes")
     */
    private ?Folder $folder;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('NOW');
        $this->updatedAt = new \DateTime('NOW');
        $this->tags = new ArrayCollection();
    }

    /**
     * Gets the unique identifier of the note.
     *
     * @return int|null The ID of the note.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Sets the ID of the note. This method is intended for testing purposes.
     *
     * @param int|null $id The ID to set.
     * @return self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Gets the title of the note.
     *
     * @return string|null The title of the note.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the title of the note.
     *
     * @param string $title The title to set.
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Gets the content of the note.
     *
     * @return string|null The content of the note.
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Sets the content of the note.
     *
     * @param string|null $content The content to set. Can be null.
     * @return self
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Appends new content to the existing note content.
     *
     * @param string $content The content to append.
     * @return self
     */
    public function addContent(string $content): self
    {
        $this->content .= $content;
        return $this;
    }

    /**
     * Gets the creation timestamp of the note.
     *
     * @return DateTimeImmutable|null The creation timestamp.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation timestamp of the note.
     *
     * @param DateTimeImmutable $createdAt The creation timestamp to set.
     * @return self
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Gets the last updated timestamp of the note.
     *
     * @return \DateTimeInterface|null The last updated timestamp.
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Sets the last updated timestamp of the note.
     *
     * @param \DateTimeInterface $updatedAt The last updated timestamp to set.
     * @return self
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Gets the category associated with the note.
     *
     * @return Category|null The associated category.
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Sets the category of the note.
     *
     * @param Category|null $category The category to associate with the note.
     * @return self
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Gets the prompt associated with the note.
     *
     * @return Prompt|null The associated prompt.
     */
    public function getPrompt(): ?Prompt
    {
        return $this->prompt;
    }

    /**
     * Sets the prompt of the note.
     *
     * @param Prompt|null $prompt The prompt to associate with the note.
     * @return self
     */
    public function setPrompt(?Prompt $prompt): self
    {
        $this->prompt = $prompt;
        return $this;
    }

    /**
     * Gets all tags associated with the note as an ArrayCollection.
     *
     * @return ArrayCollection The collection of associated tags.
     */
    public function getTags(): ArrayCollection
    {
        return ConversionUtil::convertCollectionIntoArrayCollection($this->tags);
    }

    /**
     * Adds a tag to the note if it is not already associated.
     *
     * @param Tag $tag The tag to add.
     * @return self
     */
    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addNote($this);
        }

        return $this;
    }

    /**
     * Removes a tag from the note if it is associated.
     *
     * @param Tag $tag The tag to remove.
     * @return self
     */
    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeNote($this);
        }

        return $this;
    }

    /**
     * Gets the folder associated with the note.
     *
     * @return Folder|null The associated folder.
     */
    public function getFolder(): ?Folder
    {
        return $this->folder;
    }

    /**
     * Sets the folder of the note.
     *
     * @param Folder|null $folder The folder to associate with the note.
     * @return self
     */
    public function setFolder(?Folder $folder): self
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * Serializes the note to JSON format, including or excluding specific attributes.
     *
     * @param bool $withContent Whether to include the content of the note.
     * @param bool $withCategory Whether to include the associated category.
     * @param bool $withPrompt Whether to include the associated prompt.
     * @param bool $withTags Whether to include associated tags.
     * @param bool $withFolder Whether to include the associated folder.
     *
     * @return array The serialized note as an associative array.
     */
    public function jsonSerialize(bool $withContent = true, bool $withCategory = true, bool $withPrompt = true,
                                  bool $withTags = true, bool $withFolder = true): array
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
