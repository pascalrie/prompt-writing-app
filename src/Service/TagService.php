<?php

namespace App\Service;

use App\Entity\Note;
use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Service\Factory\IService;

class TagService implements IService
{
    public TagRepository $tagRepository;

    /**
     * @param TagRepository $tagRepository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @param string $title
     * @param array|null $notes
     * @param string $color
     * @return Tag
     */
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

    /**
     * @param int $tagId
     * @param string $title
     * @param array|null $potentialNewNotes
     * @param string $color
     * @return void
     */
    public function update(int $tagId, string $title = "", array $potentialNewNotes = [], string $color = ""): Tag
    {
        $tagFromDb = $this->tagRepository->findBy(['id' => $tagId])[0];
        if ("" !== $title) {
            $tagFromDb->setTitle($title);
        }

        if (null !== $potentialNewNotes) {
            foreach ($potentialNewNotes as $note) {
                if ($note instanceof Note) {
                    $tagFromDb->addNote($note);
                }
            }
        }

        if ("" !== $color) {
            $tagFromDb->setColor($color);
        }

        $this->tagRepository->flush();

        return $tagFromDb;
    }

    /**
     * @return array
     */
    public function list(): array
    {
        return $this->tagRepository->findAll();
    }

    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id)
    {
        $tag = $this->tagRepository->findBy(['id' => $id])[0];
        $this->tagRepository->remove($tag);
    }

    /**
     * @param int $id
     * @return Tag|null
     */
    public function show(int $id): ?Tag
    {
        $tags = $this->tagRepository->findBy(['id' => $id]);
        if (empty($tags)) {
            return null;
        }
        return $tags[0];
    }

    /**
     * @param string $criteria
     * @param $argument
     * @return Tag|null
     */
    public function showBy(string $criteria, $argument): ?Tag
    {
        $tags = $this->tagRepository->findBy([$criteria => $argument]);
        if (empty($tags)) {
            return null;
        }
        return $tags[0];
    }

    /**
     * @param Note|null $note
     * @param Tag|null $tag
     * @return void
     */
    public function removeFromNote(?Note $note, ?Tag $tag)
    {
        $tag->removeNote($note);
        if ($tag->getNotes()->isEmpty()) {
            $this->tagRepository->remove($tag);
        }
        $this->tagRepository->flush();
    }
}