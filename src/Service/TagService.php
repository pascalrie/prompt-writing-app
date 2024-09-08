<?php

namespace App\Service;

use App\Entity\Note;
use App\Entity\Tag;
use App\Repository\TagRepository;

class TagService
{
    public TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function create(string $title = "", array $notes = null, string $color = ""): Tag
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

        return $tag;
    }

    public function update(int $tagId, string $title = "", array $potentialNewNotes = null, string $color = "")
    {
        $tagFromDb = $this->tagRepository->findBy(['id' => $tagId])[0];
        if ("" !== $title) {
            $tagFromDb->setTitle($title);
        }

        if (null !== $potentialNewNotes) {
            foreach ($potentialNewNotes as $note) {
                $tagFromDb->addNote($note);
            }
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

    public function show(int $id): ?Tag
    {
        $tags = $this->tagRepository->findBy(['id' => $id]);
        if (empty($tags)) {
            return null;
        }
        return $tags[0];
    }

    public function showBy(string $criteria, $argument): ?Tag
    {
        $tags = $this->tagRepository->findBy([$criteria => $argument]);
        if (empty($tags)) {
            return null;
        }
        return $tags[0];
    }

    public function removeFromNote(?Note $note, ?Tag $tag)
    {
        $tag->removeNote($note);
        if ($tag->getNotes()->isEmpty()) {
            $this->tagRepository->remove($tag);
        }
        $this->tagRepository->flush();
    }
}