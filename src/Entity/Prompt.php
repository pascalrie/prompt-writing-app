<?php

namespace App\Entity;

use App\Repository\PromptRepository;
use App\Util\ConversionUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PromptRepository::class)
 */
class Prompt
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
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="prompts")
     * @ORM\JoinColumn(nullable=false)
     */
    private Category $category;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="prompt")
     */
    private Collection $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getNotes(): ArrayCollection
    {
        return ConversionUtil::convertCollectionIntoArrayCollection($this->notes);
    }

    public function addNote(Note $note): self
    {
        if (!$this->getNotes()->contains($note)) {
            $this->notes[] = $note;
            $note->setPrompt($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->getNotes()->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getPrompt() === $this) {
                $note->setPrompt(null);
            }
        }

        return $this;
    }

    /**
     * @param bool $withCategory
     * @param bool $withNotes
     * @return array|string[]
     */
    public function jsonSerialize(bool $withCategory = true, bool $withNotes = false): array
    {
        $json = [
            'title' => $this->title,
        ];

        if ($withCategory) {
            $json['category'] = $this->category->jsonSerialize();
        }

        if ($withNotes) {
            /**@var Note $note */
            foreach ($this->getNotes() as $note) {
                $json += ['Note with id: ' . $note->getId() => $note->jsonSerialize(false, true, true, false)];
            }
        }

        return $json;
    }
}
