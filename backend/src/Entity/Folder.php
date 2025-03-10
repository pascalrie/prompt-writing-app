<?php

namespace App\Entity;

use App\Repository\FolderRepository;
use App\Util\ConversionUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a Folder entity with a collection of associated notes.
 *
 * @ORM\Entity(repositoryClass=FolderRepository::class)
 */
class Folder
{
    /**
     * The unique identifier of the folder.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * The title of the folder.
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * A collection of associated Note entities belonging to this folder.
     *
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="folder")
     */
    private Collection $notes;

    /**
     * Constructs a new Folder instance and initializes the notes collection.
     */
    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    /**
     * Sets the ID of the folder. Intended for testing purposes only.
     *
     * @param int|null $id The ID to set.
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Gets the ID of the folder.
     *
     * @return int|null The ID of the folder or null if not set.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the title of the folder.
     *
     * @return string|null The title of the folder.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the title of the folder.
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
     * Gets all notes associated with the folder as an ArrayCollection.
     *
     * @return ArrayCollection The collection of associated notes.
     */
    public function getNotes(): ArrayCollection
    {
        return ConversionUtil::convertCollectionIntoArrayCollection($this->notes);
    }

    /**
     * Adds a note to the folder. Sets the folder reference in the note.
     *
     * @param Note $note The note to add.
     * @return $this
     */
    public function addNote(Note $note): self
    {
        if (!$this->getNotes()->contains($note)) {
            $this->notes[] = $note;
            $note->setFolder($this);
        }

        return $this;
    }

    /**
     * Removes a note from the folder. Unsets the folder reference in the note.
     *
     * @param Note $note The note to remove.
     * @return $this
     */
    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getFolder() === $this) {
                $note->setFolder(null);
            }
        }

        return $this;
    }

    /**
     * Serializes the folder to an array representation.
     *
     * @param bool $withNotes Whether to include associated notes in the serialization.
     * @return array|string[] The serialized folder as an array.
     */
    public function jsonSerialize(bool $withNotes = false): array
    {
        $json = [
            'id' => $this->id,
            'title' => $this->title
        ];

        if ($withNotes) {
            $notesArray = [];

            /** @var Note $note */
            foreach ($this->getNotes() as $note) {
                $notesArray[] = $note->jsonSerialize(true, false, true, false);
            }

            $json['notes'] = $notesArray;
        }

        return $json;
    }

    /**
     * Removes all notes associated with the folder.
     */
    public function clearNotes()
    {
        foreach ($this->notes as $note) {
            $this->removeNote($note);
        }
    }
}
