<?php

namespace App\Service;

use App\Entity\Folder;
use App\Entity\Note;
use App\Repository\FolderRepository;
use Doctrine\ORM\EntityNotFoundException;

class FolderService implements IService
{
    protected FolderRepository $folderRepository;

    /**
     * Constructor for FolderService.
     *
     * @param FolderRepository $folderRepository A repository instance for Folder entity.
     */
    public function __construct(FolderRepository $folderRepository)
    {
        $this->folderRepository = $folderRepository;
    }

    /**
     * Creates a new Folder entity with an optional first Note.
     *
     * @param string $title The title of the Folder.
     * @param Note|null $firstNote An optional Note to associate with the Folder upon creation.
     * @return Folder The created Folder instance.
     */
    public function create(string $title, ?Note $firstNote = null): Folder
    {
        $folder = new Folder();
        $folder->setTitle($title);

        if ($firstNote) {
            $folder->addNote($firstNote);
        }

        $this->folderRepository->add($folder, true); // Persist and flush in one step

        return $folder;
    }

    /**
     * Updates an existing Folder entity by its ID with new details or Notes.
     *
     * @param int $folderId The ID of the Folder to update.
     * @param string $newTitle The new title for the Folder. Optional, can be empty if no title update is needed.
     * @param Note[] $potentialNewNotes An array of Note entities to associate with the Folder. Optional.
     * @param bool $notesShouldBeReplaced Indicates whether the existing Notes should be replaced with the new Notes.
     * @return Folder The updated Folder instance.
     * @throws EntityNotFoundException If no Folder entity is found for the given ID.
     */
    public function update(int $folderId, string $newTitle = "", array $potentialNewNotes = [], bool $notesShouldBeReplaced = false): Folder
    {
        $folder = $this->folderRepository->find($folderId);

        if (!$folder) {
            throw new EntityNotFoundException('Folder not found for the given ID.');
        }

        if ($newTitle !== "") {
            $folder->setTitle($newTitle);
        }

        if ($notesShouldBeReplaced) {
            $folder->clearNotes();

            foreach ($potentialNewNotes as $note) {
                if ($note instanceof Note) {
                    $folder->addNote($note);
                    $note->setFolder($folder);
                }
            }
        } elseif (!empty($potentialNewNotes)) {
            foreach ($potentialNewNotes as $note) {
                if ($note instanceof Note) {
                    $folder->addNote($note);
                    $note->setFolder($folder);
                }
            }
        }

        $this->folderRepository->add($folder, true);

        return $folder;
    }

    /**
     * Retrieves a list of all Folder entities.
     *
     * @return Folder[] An array of all Folder entities.
     */
    public function list(): array
    {
        return $this->folderRepository->findAll();
    }

    /**
     * Deletes a Folder entity by its ID.
     *
     * @param int $id The ID of the Folder to delete.
     * @return void
     * @throws EntityNotFoundException If no Folder entity is found for the given ID.
     */
    public function delete(int $id): void
    {
        $folder = $this->folderRepository->find($id);

        if (!$folder) {
            throw new EntityNotFoundException('Folder not found for the given ID.');
        }

        $this->folderRepository->remove($folder, true); // Remove and flush in one step
    }

    /**
     * Retrieves the details of a Folder entity by its ID.
     *
     * @param int $id The ID of the Folder to retrieve.
     * @return Folder|null The Folder entity, or null if not found.
     */
    public function show(int $id): ?Folder
    {
        return $this->folderRepository->findOneBy(['id' => $id]);
    }

    /**
     * Finds a Folder entity by arbitrary criteria.
     *
     * @param string $criteria The field name to use for the search.
     * @param mixed $argument The value of the field to search for.
     * @return Folder|null The Folder entity matching the criteria, or null if not found.
     * @throws \InvalidArgumentException If the criteria is invalid or empty.
     */
    public function showBy(string $criteria, $argument): ?Folder
    {
        if (empty($criteria)) {
            throw new \InvalidArgumentException('Criteria must be a valid field.');
        }

        $result = $this->folderRepository->findOneBy([$criteria => $argument]);

        return $result ?: null;
    }
}