<?php

namespace App\Service;

use App\Entity\Folder;
use App\Entity\Note;
use App\Repository\FolderRepository;
use Doctrine\ORM\EntityNotFoundException;

class FolderService implements IService
{
    protected FolderRepository $folderRepository;

    public function __construct(FolderRepository $folderRepository)
    {
        $this->folderRepository = $folderRepository;
    }

    /**
     * Creates a new Folder with an optional first Note.
     *
     * @param string $title
     * @param Note|null $firstNote
     * @return Folder
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
     * Updates an existing Folder by its ID.
     *
     * @param int $folderId
     * @param string $newTitle
     * @param Note[] $potentialNewNotes
     * @param bool $notesShouldBeReplaced
     * @return Folder
     * @throws EntityNotFoundException if the folder is not found
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
     * Lists all Folders.
     *
     * @return Folder[]
     */
    public function list(): array
    {
        return $this->folderRepository->findAll();
    }

    /**
     * Deletes a Folder by its ID.
     *
     * @param int $id
     * @return void
     * @throws EntityNotFoundException if the folder is not found
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
     * Shows details of a Folder by its ID.
     *
     * @param int $id
     * @return Folder|null
     */
    public function show(int $id): ?Folder
    {
        return $this->folderRepository->findOneBy(['id' => $id]);
    }

    /**
     * Finds a Folder by arbitrary criteria.
     *
     * @param string $criteria
     * @param mixed $argument
     * @return Folder|null
     * @throws \InvalidArgumentException if the criteria or argument is invalid
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