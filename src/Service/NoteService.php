<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Tag;
use App\Repository\NoteRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;

class NoteService
{
    protected NoteRepository $noteRepository;

    public function __construct(NoteRepository $categoryRepository)
    {
        $this->noteRepository = $categoryRepository;
    }

    public function create(string $title, string $content = "", ArrayCollection $tags = null, Category $category = null): Note
    {
        $note = new Note();

        $note->setCreatedAt(new DateTimeImmutable('NOW'));
        $note->setUpdatedAt(new DateTime('NOW'));
        $note->setTitle($title);

        if ("" !== $content ) {
            $note->setContent($content);
        }

        if (null !== $tags) {
            foreach ($tags as $tag) {
                $note->addTag($tag);
            }
        }

        if (null !== $category) {
            $note->setCategory($category);
        }

        $this->noteRepository->add($note);
        return $note;
    }

    public function update(int $noteId, string $newTitle = "", string $newContent = "", array $newTags = null, Category $newCategory = null): void
    {
        $noteFromDb = $this->noteRepository->findBy(['id' => $noteId])[0];

        if ("" !== $newTitle) {
            $noteFromDb->setTitle($newTitle);
        }

        if ("" !== $newContent) {
            $noteFromDb->setContent($newContent);
        }

        if (null !== $newTags) {
            $this->addTagsIfNotAssociatedToNote($newTags, $noteFromDb);
        }

        if (null !== $newCategory) {
            $noteFromDb->setCategory($newCategory);
        }

        $noteFromDb->setUpdatedAt(new DateTime('NOW'));
        $this->noteRepository->flush();
    }

    public function list(): array
    {
        return $this->noteRepository->findAll();
    }

    public function delete(int $id): void
    {
        $category = $this->noteRepository->findBy(['id' => $id])[0];
        $this->noteRepository->remove($category);
    }

    public function show(int $id): Note
    {
        return $this->noteRepository->findBy(['id' => $id])[0];
    }

    private function isTagAlreadyAssociatedToNote(int $noteId, int $tagId): bool
    {
        $tagsForGivenNote = $this->noteRepository->findBy(['id' => $noteId])[0]->getTags();
        foreach ($tagsForGivenNote as $tagForNote) {
            if ($tagForNote->getId() === $tagId) {
                return true;
            }
        }
        return false;
    }

    private function addTagsIfNotAssociatedToNote(array $tags, Note $note): void
    {
        foreach ($tags as $tagToAdd) {
            $isAssociated = $this->isTagAlreadyAssociatedToNote($note->getId(), $tagToAdd->getId());
            if (!$isAssociated) {
                $note->addTag($tagToAdd);
            }
        }
    }
}