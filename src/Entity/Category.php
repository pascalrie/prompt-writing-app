<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Util\ConversionUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
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
     * @ORM\OneToMany(targetEntity=Prompt::class, mappedBy="category")
     */
    private Collection $prompts;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="category")
     */
    private Collection $notes;

    public function __construct()
    {
        $this->prompts = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    /**
     * only for testing
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrompts(): ArrayCollection
    {
        return ConversionUtil::convertCollectionIntoArrayCollection($this->prompts);
    }

    public function addPrompt(Prompt $prompt): self
    {
        if (!$this->getPrompts()->contains($prompt)) {
            $this->prompts[] = $prompt;
            $prompt->setCategory($this);
        }
        return $this;
    }

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


    public function getNotes(): ArrayCollection
    {
        return ConversionUtil::convertCollectionIntoArrayCollection($this->notes);
    }

    public function addNote(Note $note): self
    {
        if (!$this->getNotes()->contains($note)) {
            $this->notes[] = $note;
            $note->setCategory($this);
        }

        return $this;
    }

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
     * @param bool $withPrompts
     * @param bool $withNotes
     * @return array|string[]
     */
    public function jsonSerialize(bool $withPrompts = false, bool $withNotes = false): array
    {
        $json = [
            'id' => $this->id,
            'title' => $this->title
        ];

        if ($withPrompts) {
            /** @var Prompt $prompt */
            foreach ($this->getPrompts() as $prompt) {
                $json += ['Prompt with id: ' . $prompt->getId() => $prompt->jsonSerialize(false)];
            }
        }

        if ($withNotes) {
            /**@var Note $note */
            foreach ($this->getNotes() as $note) {
                $json += ['Note with id: ' . $note->getId() => $note->jsonSerialize(false,
                    false, false, false)];
            }
        }

        return $json;
    }
}
