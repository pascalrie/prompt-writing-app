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
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $color = null;

    /**
     * @ORM\ManyToMany(targetEntity=Note::class, inversedBy="tags")
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

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
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        $this->getNotes()->removeElement($note);

        return $this;
    }

    /**
     * @param bool $withNotes
     * @return array
     */
    public function jsonSerialize(bool $withNotes = false): array
    {
        $json = [
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
