<?php

namespace App\Entity;

use App\Repository\PromptRepository;
use App\Util\ConversionUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a prompt entity and provides mappings to database fields and relations.
 *
 * @ORM\Entity(repositoryClass=PromptRepository::class)
 */
class Prompt
{
    /**
     * The unique identifier for the prompt.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * The title of the prompt.
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * The category to which this prompt belongs.
     *
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="prompts")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Category $category;

    /**
     * A collection of notes associated with this prompt.
     *
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="prompt", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $notes;

    /**
     * Initializes the Prompt entity with an empty collection of notes.
     */
    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    /**
     * Gets the unique identifier of the prompt.
     *
     * @return int|null Returns the ID of the prompt, or null if it has not been set.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Sets the unique identifier for the prompt.
     * Primarily for testing purposes.
     *
     * @param int|null $id The ID to set for the prompt.
     * @return self Returns the current instance for method chaining.
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Gets the title of the prompt.
     *
     * @return string|null Returns the title of the prompt.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the title of the prompt.
     *
     * @param string $title The title to set for the prompt.
     * @return self Returns the current instance for method chaining.
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the category of the prompt.
     *
     * @return Category|null Returns the category to which this prompt belongs.
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Sets the category of the prompt.
     *
     * @param Category|null $category The category to associate with this prompt.
     * @return self Returns the current instance for method chaining.
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Gets all notes associated with this prompt as an ArrayCollection.
     *
     * @return ArrayCollection Returns the notes associated with this prompt.
     */
    public function getNotes(): ArrayCollection
    {
        return ConversionUtil::convertCollectionIntoArrayCollection($this->notes);
    }

    /**
     * Adds a note to the collection of notes linked to this prompt.
     *
     * @param Note $note The note to associate with this prompt.
     * @return self Returns the current instance for method chaining.
     */
    public function addNote(Note $note): self
    {
        if (!$this->getNotes()->contains($note)) {
            $this->notes[] = $note;
            $note->setPrompt($this);
        }

        return $this;
    }

    /**
     * Removes a note from the collection of notes linked to this prompt.
     *
     * @param Note $note The note to dissociate from this prompt.
     * @return self Returns the current instance for method chaining.
     */
    public function removeNote(Note $note): self
    {
        if ($this->getNotes()->removeElement($note)) {
            if ($note->getPrompt() === $this) {
                $note->setPrompt(null);
            }
        }

        return $this;
    }

    /**
     * Serializes the prompt into an array for JSON representation.
     *
     * @param bool $withCategory Whether to include the category information in the serialization.
     * @param bool $withNotes Whether to include the notes associated with this prompt in the serialization.
     * @return array|string[] Returns an array representation of the prompt.
     */
    public function jsonSerialize(bool $withCategory = true, bool $withNotes = true): array
    {
        $json = [
            'id' => $this->id,
            'title' => $this->title,
        ];

        if ($withCategory) {
            $json['category'] = $this->category->jsonSerialize();
        }

        if ($withNotes) {
            /** @var Note $note */
            foreach ($this->getNotes() as $note) {
                $json['notes'][] = $note->jsonSerialize();
            }
        }

        return $json;
    }
}
