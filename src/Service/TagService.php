<?php

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;

class TagService
{
    protected TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function create(string $title = "", array $notes = null, string $color = "")
    {
        $tag = new Tag();

        if ("" !== $title) {
            $tag->setTitle($title);
        }

        if (null !== $notes) {
            foreach ($notes as $note) {
                $tag->addNote($note);
            }
        }

        if ("" !== $color) {
            $tag->setColor($color);
        }

        $this->tagRepository->add($tag);
    }

    public function update(int $tagId, string $title = "", array $notes = null, string $color = "")
    {
        $tagFromDb = $this->tagRepository->findBy(['id' => $tagId])[0];
        if ("" !== $title) {
            $tagFromDb->setTitle($title);
        }

        if (null !== $notes) {
            $this->addNotesIfNotAssociatedToTag($notes, $tagFromDb);
        }

        if ("" !== $color) {
            $tagFromDb->setColor($color);
        }

        $this->tagRepository->flush();
    }

    public function list(): array
    {
        return $this->tagRepository->findAll();
    }

    public function delete(int $id)
    {
        $tag = $this->tagRepository->findBy(['id' => $id])[0];
        $this->tagRepository->remove($tag);
    }

    public function show(int $id): Tag
    {
        return $this->tagRepository->findBy(['id' => $id])[0];
    }

    private function isNoteAlreadyAssociatedToTag(int $noteId, int $tagId): bool
    {
        $notesForGivenTag = $this->tagRepository->findBy(['id' => $tagId])[0]->getNotes();
        foreach ($notesForGivenTag as $note) {
            if ($note->getId() === $noteId) {
                return true;
            }
        }
        return false;
    }

    private function addNotesIfNotAssociatedToTag(array $notes, Tag $tag): void
    {
        foreach ($notes as $note) {
            $isAssociated = $this->isNoteAlreadyAssociatedToTag($note->getId(), $tag->getId());
            if (!$isAssociated) {
                $tag->addNote($note);
            }
        }
    }
}