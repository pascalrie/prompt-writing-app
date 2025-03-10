<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Tag;
use App\Repository\CategoryRepository;
use App\Repository\NoteRepository;
use App\Repository\PromptRepository;
use App\Repository\TagRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use InvalidArgumentException;

class NoteService implements IService
{
    private NoteRepository $noteRepository;

    private TagRepository $tagRepository;

    private CategoryRepository $categoryRepository;

    private PromptRepository $promptRepository;

    public function __construct(NoteRepository     $noteRepository, TagRepository $tagRepository,
                                CategoryRepository $categoryRepository, PromptRepository $promptRepository)
    {
        $this->noteRepository = $noteRepository;

        $this->tagRepository = $tagRepository;

        $this->categoryRepository = $categoryRepository;

        $this->promptRepository = $promptRepository;
    }

    /**
     * Creates and persists a new note with the given attributes.
     *
     * @param string $title The title of the note. If empty, a default title will be generated from the content.
     * @param string $content The content of the note.
     * @param int|null $promptId The id of the associated prompt.
     * @param ArrayCollection|null $tags A collection of tags to be associated with the note.
     * @param Category|null $category The category to assign the note to.
     * @return Note The newly created note entity.
     */
    public function create(string $title = "", string $content = "", int $promptId = null, ?ArrayCollection $tags = null,
                           ?Category $category = null): Note
    {
        $note = new Note();

        // Automatically generate title if not provided
        $note->setTitle($title ?: substr($content, 0, 15) . '...');

        $note->setContent($content);
        if ($promptId !== null) {
            $prompt = $this->promptRepository->findOneBy(['id' => $promptId]);
            $note->setPrompt($prompt);
        }

        if ($tags !== null) {
            foreach ($tags as $tag) {
                if ($tag instanceof Tag) {
                    $note->addTag($tag);
                }
            }
        }

        if ($category !== null) {
            $note->setCategory($category);
        }

        $this->noteRepository->add($note, true);

        return $note;
    }

    /**
     * Updates an existing note with the given parameters.
     *
     * @param int $noteId The ID of the note to update.
     * @param string $newTitle The new title for the note, if provided.
     * @param string $content The updated content for the note.
     * @param bool $contentShouldBeAdded Whether the provided content should be appended to the current content.
     * @param bool $contentShouldBeRemoved Whether the content of the note should be cleared.
     * @param array $newTags A list of tag titles to associate with the note.
     * @param string|null $newCategoryTitle The title of the new category to associate with the note, if applicable.
     * @return Note The updated note entity.
     * @throws EntityNotFoundException If the note with the specified ID is not found.
     */
    public function update(int  $noteId, string $newTitle = "", string $content = "", bool $contentShouldBeAdded = false,
                           bool $contentShouldBeRemoved = false, array $newTags = [], ?string $newCategoryTitle = null): Note
    {
        $note = $this->noteRepository->findOneBy(['id' => $noteId]);

        if (!$note) {
            throw new EntityNotFoundException("Note with ID {$noteId} not found.");
        }

        if ($contentShouldBeAdded && $content) {
            $note->addContent($content);
        } elseif ($contentShouldBeRemoved) {
            $note->setContent("");
        } elseif ($content) {
            $note->setContent($content);
        }

        if ($newTitle) {
            $note->setTitle($newTitle);
        }

        if (!empty($newTags)) {
            foreach ($newTags as $tagTitle) {
                $tag = $this->tagRepository->findOneBy(['title' => $tagTitle]);
                if ($tag instanceof Tag) {
                    $note->addTag($tag);
                }
            }
        }

        if ($newCategoryTitle) {
            $category = $this->categoryRepository->findOneBy(['title' => $newCategoryTitle]);
            if ($category instanceof Category) {
                $note->setCategory($category);
            }
        }

        $note->setUpdatedAt(new DateTime('NOW'));
        $this->noteRepository->flush();

        return $note;
    }

    /**
     * Retrieves all notes stored in the repository.
     *
     * @return Note[] An array of all note entities.
     */
    public function list(): array
    {
        return $this->noteRepository->findAll();
    }

    /**
     * Deletes a note with the given ID.
     *
     * @param int $id The ID of the note to delete.
     * @return void
     * @throws EntityNotFoundException If the note with the specified ID is not found.
     */
    public function delete(int $id): void
    {
        $note = $this->noteRepository->findOneBy(['id' => $id]);

        if (!$note) {
            throw new EntityNotFoundException("Note with ID {$id} not found.");
        }

        $this->noteRepository->remove($note, true);
    }

    /**
     * Retrieves a specific note by its ID.
     *
     * @param int $id The ID of the note to retrieve.
     * @return Note|null The note entity or null if not found.
     */
    public function show(int $id): ?Note
    {
        return $this->noteRepository->findOneBy(['id' => $id]);
    }

    /**
     * Retrieves a specific note by an arbitrary criteria.
     *
     * @param string $criteria The name of the attribute to use as a filter (e.g., "title", "id").
     * @param mixed $argument The value to match for the specified criteria.
     * @return Note|null The note entity or null if no matching record is found.
     */
    public function showBy(string $criteria, $argument): ?Note
    {
        return $this->noteRepository->findOneBy([$criteria => $argument]);
    }

    /**
     * Removes a tag from the given note.
     *
     * @param Note|null $note The note entity to remove the tag from.
     * @param Tag|null $tag The tag entity to be removed.
     * @return void
     * @throws InvalidArgumentException If the note or tag is null.
     */
    public function removeTagFromNote(?Note $note, ?Tag $tag): void
    {
        if (!$note || !$tag) {
            throw new InvalidArgumentException("Note or Tag cannot be null.");
        }

        $note->removeTag($tag);
        $this->noteRepository->flush();
    }
}