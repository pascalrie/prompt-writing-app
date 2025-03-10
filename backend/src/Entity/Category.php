<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Util\ConversionUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a category entity which holds associated prompts and notes.
 *
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * The unique identifier of the category.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * The title of the category.
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * The collection of prompts associated with this category.
     *
     * @ORM\OneToMany(targetEntity=Prompt::class, mappedBy="category")
     */
    private Collection $prompts;

    /**
     * The collection of notes associated with this category.
     *
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="category")
     */
    private Collection $notes;

    /**
     * Initializes the collections for prompts and notes.
     */
    public function __construct()
    {
        $this->prompts = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    /**
     * Sets the ID of the category (only for testing purposes).
     *
     * @param int|null $id The ID to be set.
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Gets the unique identifier of the category.
     *
     * @return int|null The ID of the category.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the title of the category.
     *
     * @return string|null The title of the category.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the title of the category.
     *
     * @param string $title The title to set.
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the collection of prompts associated with this category as an ArrayCollection.
     *
     * @return ArrayCollection The converted collection of prompts.
     */
    public function getPrompts(): ArrayCollection
    {
        return ConversionUtil::convertCollectionIntoArrayCollection($this->prompts);
    }

    /**
     * Adds a prompt to the category and associates it.
     *
     * @param Prompt $prompt The prompt to add.
     * @return $this
     */
    public function addPrompt(Prompt $prompt): self
    {
        if (!$this->getPrompts()->contains($prompt)) {
            $this->prompts[] = $prompt;
            $prompt->setCategory($this);
        }
        return $this;
    }

    /**
     * Removes a prompt from the category and disassociates it.
     *
     * @param Prompt $prompt The prompt to remove.
     * @return $this
     */
    public function removePrompt(Prompt $prompt): self
    {
        if ($this->prompts->removeElement($prompt)) {
            if ($prompt->getCategory() === $this) {
                $prompt->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * Removes all prompts associated with this category.
     *
     * @return void
     */
    public function clearPrompts(): void
    {
        foreach ($this->prompts as $prompt) {
            $this->removePrompt($prompt);
        }
    }

    /**
     * Gets the collection of notes associated with this category as an ArrayCollection.
     *
     * @return ArrayCollection The converted collection of notes.
     */
    public function getNotes(): ArrayCollection
    {
        return ConversionUtil::convertCollectionIntoArrayCollection($this->notes);
    }

    /**
     * Adds a note to the category and associates it.
     *
     * @param Note $note The note to add.
     * @return $this
     */
    public function addNote(Note $note): self
    {
        if (!$this->getNotes()->contains($note)) {
            $this->notes[] = $note;
            $note->setCategory($this);
        }

        return $this;
    }

    /**
     * Removes a note from the category and disassociates it.
     *
     * @param Note $note The note to remove.
     * @return $this
     */
    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            if ($note->getCategory() === $this) {
                $note->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * Serializes the category into a JSON-compatible array.
     *
     * @param bool $withPrompts Whether to include associated prompts in the serialization.
     * @param bool $withNotes Whether to include associated notes in the serialization.
     * @return array The serialized data of the category.
     */
    public function jsonSerialize(bool $withPrompts = false, bool $withNotes = false): array
    {
        $json = [
            'id' => $this->id,
            'title' => $this->title
        ];
        $json['prompts'] = [];
        if ($withPrompts) {
            /** @var Prompt $prompt */
            foreach ($this->getPrompts() as $prompt) {
                $json['prompts'][] = [$prompt->jsonSerialize(false, false)];
            }
        }

        $json['notes'] = [];
        if ($withNotes) {
            /**@var Note $note */
            foreach ($this->getNotes() as $note) {
                $json['notes'][] = [$note->jsonSerialize(true, false, false, false)];
            }
        }

        return $json;
    }
}
