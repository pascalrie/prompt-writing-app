<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
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
    private ArrayCollection $prompts;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="category")
     */
    private ArrayCollection $notes;

    public function __construct()
    {
        $this->prompts = new ArrayCollection();
        $this->notes = new ArrayCollection();
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

    /**
     * @return Collection<int, Prompt>
     */
    public function getPrompts(): Collection
    {
        return $this->prompts;
    }

    public function addPrompt(Prompt $prompt): self
    {
        if (!$this->prompts->contains($prompt)) {
            $this->prompts[] = $prompt;
            $prompt->setCategory($this);
        }

        return $this;
    }

    public function removePrompt(Prompt $prompt): self
    {
        if ($this->prompts->removeElement($prompt)) {
            // set the owning side to null (unless already changed)
            if ($prompt->getCategory() === $this) {
                $prompt->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setCategory($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getCategory() === $this) {
                $note->setCategory(null);
            }
        }

        return $this;
    }

    public function jsonSerialize(bool $withPrompts = false, bool $withNotes = false): array
    {
        $json = [
            'title' => $this->title
        ];

        if ($withPrompts) {
            /** @var Prompt $prompt */
            foreach ($this->prompts as $prompt) {
                $json += ['Prompt with id: ' . $prompt->getId() => $prompt->jsonSerialize(false)];
            }
        }

        if ($withNotes) {
            /**@var Note $note */
            foreach ($this->notes as $note) {
                $json += ['Note with id: ' . $note->getId() => $note->jsonSerialize(false, false, false, false)];
            }
        }

        return $json;
    }
}
