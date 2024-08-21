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

    public function create(string $title = "", string $content = "", ArrayCollection $tags = null, Category $category = null): Note
    {
        $note = new Note();

        $note->setCreatedAt(new DateTimeImmutable('NOW'));
        $note->setUpdatedAt(new DateTime('NOW'));
        if (null === $title) {
            $title = substr($content, 0, 15) . '...';
        }

        $note->setTitle($title);

        $note->setContent($content);

        if (!$tags->isEmpty()) {
            $tagsForForeach = $tags->toArray();
            foreach ($tagsForForeach as $tag) {
                if (null !== $tag) {
                    $note->addTag($tag);
                }
            }
        }

        if (null !== $category) {
            $note->setCategory($category);
        }

        $this->noteRepository->add($note, true);
        return $note;
    }

    public function update(int $noteId, string $content, bool $contentIsAdded = false, string $newTitle = "", array $newTags = null, Category $newCategory = null): void
    {
        $noteFromDb = $this->noteRepository->findBy(['id' => $noteId])[0];

        if ($contentIsAdded) {
            $noteFromDb->addContent($content);
        }

        if (!$contentIsAdded) {
            $noteFromDb->setContent($content);
        }

        if (null === $newTitle) {
            $newTitle = substr($noteFromDb->getContent(), 0, 15) . '...';
        }
        $noteFromDb->setTitle($newTitle);

        if (null !== $newTags) {
            foreach ($newTags as $tag) {
                $noteFromDb->addTag($tag);
            }
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
        $note = $this->noteRepository->findBy(['id' => $id])[0];
        $this->noteRepository->remove($note, true);
    }

    public function show(int $id): ?Note
    {
        $notes = $this->noteRepository->findBy(['id' => $id]);
        if (empty($notes)) {
            return null;
        }

        return $notes[0];
    }

    public function showBy(string $criteria, $argument): ?Note
    {
        $notes = $this->noteRepository->findBy([$criteria => $argument]);
        if (empty($notes)) {
            return null;
        }
        return $notes[0];
    }

    public function removeTagFromNote(?Note $note, ?Tag $tag)
    {
        $note->removeTag($tag);
        $this->noteRepository->flush();
    }
}