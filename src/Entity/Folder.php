<?php

namespace App\Entity;

use App\Repository\FolderRepository;
use App\Util\ConversionUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FolderRepository::class)
 */
class Folder implements IEntity
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
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="folder")
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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
            $note->setFolder($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->getNotes()->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getFolder() === $this) {
                $note->setFolder(null);
            }
        }

        return $this;
    }

    public function jsonSerialize(bool $withNotes = false): array
    {
        $json = [
            'title' => $this->title
        ];

        if ($withNotes) {
            /**@var Note $note */
            foreach ($this->getNotes() as $note) {
                $json += ['Note with id: ' . $note->getId() => $note->jsonSerialize(false, true, true, false)];
            }
        }

        return $json;
    }
}
