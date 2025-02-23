<?php

namespace App\Entity;

use App\Repository\TagRepository;
use App\Util\ConversionUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag
{
    /**
     * @var int|null The unique identifier of the tag
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @var string|null The title of the tag
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private ?string $title;

    /**
     * @var string|null The color associated with the tag
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $color = null;

    /**
     * @var Collection<int, Note> A collection of notes associated with the tag
     *
     * @ORM\ManyToMany(targetEntity=Note::class, inversedBy="tags")
     * @ORM\JoinTable(name="tag_note")
     */
    private Collection $notes;

    /**
     * Constructor initializes the notes collection.
     */
    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    /**
     * Get the unique identifier of the tag.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the unique identifier of the tag. Only used for testing purposes.
     *
     * @param int|null $id The identifier to set
     * @return self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the title of the tag.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the title of the tag.
     *
     * @param string $title The title to set
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the color associated with the tag.
     *
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * Set the color for the tag.
     *
     * @param string|null $color The color to set
     * @return self
     */
    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get the notes associated with the tag as an ArrayCollection.
     *
     * @return ArrayCollection<int, Note>
     */
    public function getNotes(): ArrayCollection
    {
        return ConversionUtil::convertCollectionIntoArrayCollection($this->notes);
    }

    /**
     * Add a note to the tag's collection.
     *
     * @param Note $note The note to add
     * @return self
     */
    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->addTag($this);
        }

        return $this;
    }


    /**
     * Remove a note from the tag's collection.
     *
     * @param Note $note The note to remove
     * @return self
     */
    public function removeNote(Note $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            $note->removeTag($this);
        }

        return $this;
    }


    /**
     * Serialize the tag into a JSON-compatible array.
     *
     * @param bool $withNotes Whether to include associated notes in the JSON
     * @return array<string, mixed> The serialized tag
     */
    public function jsonSerialize(bool $withNotes = false): array
    {
        $json = [
            'id' => $this->id,
            'title' => $this->title,
            'color' => $this->color,
        ];

        if ($withNotes) {
            foreach ($this->getNotes() as $note) {
                $json += ['Note with id: ' . $note->getId() => $note->jsonSerialize()];
            }
        }
        return $json;
    }
}
