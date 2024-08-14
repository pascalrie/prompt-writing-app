<?php

namespace App\Service;

use App\Entity\Folder;
use App\Entity\Note;
use App\Repository\FolderRepository;

class FolderService
{
    protected FolderRepository $folderRepository;

    public function __construct(FolderRepository $folderRepository)
    {
        $this->folderRepository = $folderRepository;
    }

    public function create(string $title, Note $firstNote = null): Folder
    {
        $folder = new Folder();
        $folder->setTitle($title);
        if ($firstNote) {
            $folder->addNote($firstNote);
        }
        $this->folderRepository->add($folder);
        return $folder;
    }

    public function update(int $folderId, string $newTitle = "", array $notes = null): void
    {
        $folderEntityFromDb = $this->folderRepository->findBy(['id' => $folderId])[0];

        if (null !== $newTitle) {
            $folderEntityFromDb->setTitle($newTitle);
        }

        if (null !== $notes) {
            $this->addNotesIfNotAssociatedToFolder($notes, $folderEntityFromDb);
        }

        $this->folderRepository->flush();
    }

    public function list(): array
    {
        return $this->folderRepository->findAll();
    }

    public function delete(int $id): void
    {
        $category = $this->folderRepository->findBy(['id' => $id])[0];
        $this->folderRepository->remove($category);
    }

    public function show(int $id): Folder
    {
        return $this->folderRepository->findBy(['id' => $id])[0];
    }

    private function isNoteAlreadyAssociatedToFolder(int $folderId, int $noteId): bool
    {
        $notesForGivenFolder = $this->folderRepository->findBy(['id' => $folderId])[0]->getNotes();
        foreach ($notesForGivenFolder as $noteAlreadyInFolder) {
            if ($noteAlreadyInFolder->getId() === $noteId) {
                return true;
            }
        }
        return false;
    }

    private function addNotesIfNotAssociatedToFolder(array $notesToAdd, Folder $folder): void
    {
        foreach ($notesToAdd as $noteToAdd) {
            $isAssociated = $this->isNoteAlreadyAssociatedToFolder($folder->getId(), $noteToAdd->getId());
            if (!$isAssociated) {
                $folder->addNote($noteToAdd);
            }
        }
    }
}