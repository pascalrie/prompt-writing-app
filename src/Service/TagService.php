<?php

namespace App\Service;

use App\Entity\Note;
use App\Entity\Tag;
use App\Repository\TagRepository;
use InvalidArgumentException;

/**
 * Service class to manage Tag entities.
 */
class TagService implements IService
{
    private TagRepository $tagRepository;

    /**
     * TagService constructor.
     *
     * @param TagRepository $tagRepository Repository for Tag entities.
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Creates a new Tag entity and adds it to the database.
     *
     * @param string $title The title of the tag.
     * @param array|null $notes An array of Note entities to associate with the tag (optional).
     * @param string $color The color of the tag (optional).
     *
     * @return Tag The created Tag entity.
     */
    public function create(string $title = "", array $notes = null, string $color = ""): Tag
    {
        $tag = new Tag();

        if ($title !== "") {
            $tag->setTitle($title);
        }

        if (!empty($notes)) {
            foreach ($notes as $note) {
                if ($note instanceof Note) {
                    $tag->addNote($note);
                }
            }
        }

        if ($color !== "") {
            $tag->setColor($color);
        }

        $this->tagRepository->add($tag);

        return $tag;
    }

    /**
     * Updates an existing Tag entity.
     *
     * @param int $tagId The ID of the tag to update.
     * @param string $title The new title of the tag (optional).
     * @param array $potentialNewNotes An array of Note entities to associate with the tag (optional).
     * @param string $color The new color of the tag (optional).
     *
     * @return Tag The updated Tag entity.
     *
     * @throws InvalidArgumentException If the tag with the given ID is not found.
     */
    public function update(int $tagId, string $title = "", array $potentialNewNotes = [], string $color = ""): Tag
    {
        $tagFromDb = $this->showOneBy('id', $tagId);

        if (!$tagFromDb) {
            throw new InvalidArgumentException("Tag with ID $tagId not found.");
        }

        if ($title !== "") {
            $tagFromDb->setTitle($title);
        }

        if (!empty($potentialNewNotes)) {
            foreach ($potentialNewNotes as $note) {
                if ($note instanceof Note) {
                    $tagFromDb->addNote($note);
                }
            }
        }

        if ($color !== "") {
            $tagFromDb->setColor($color);
        }

        $this->tagRepository->flush();

        return $tagFromDb;
    }

    /**
     * Lists all Tag entities.
     *
     * @return Tag[] An array of Tag entities.
     */
    public function list(): array
    {
        return $this->tagRepository->findAll();
    }

    /**
     * Deletes a Tag entity by its ID.
     *
     * @param int $id The ID of the tag to delete.
     *
     * @throws InvalidArgumentException If the tag with the given ID is not found.
     */
    public function delete(int $id): void
    {
        $tag = $this->tagRepository->find($id);

        if (!$tag) {
            throw new InvalidArgumentException("Tag with ID $id not found.");
        }

        $this->tagRepository->remove($tag);
    }

    /**
     * Finds a Tag entity by its ID.
     *
     * @param int $id The ID of the tag.
     *
     * @return Tag|null The found Tag entity or null if not found.
     */
    public function show(int $id): ?Tag
    {
        return $this->tagRepository->findOneBy(['id' => $id]);
    }

    /**
     * Finds a Tag entity by a specific criterion.
     *
     * @param string $criteria The field name to search by.
     * @param mixed $argument The value of the field to search for.
     *
     * @return Tag|null The found Tag entity or null if not found.
     */
    public function showOneBy(string $criteria, $argument): ?Tag
    {
        return $this->tagRepository->findOneBy([$criteria => $argument]) ?? null;
    }
}