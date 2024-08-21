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

    public function update(int $folderId, string $newTitle = "", array $potentialNewNotes = null): Folder
    {
        $folderEntityFromDb = $this->folderRepository->findBy(['id' => $folderId])[0];

        if (null !== $newTitle) {
            $folderEntityFromDb->setTitle($newTitle);
        }

        if (null !== $potentialNewNotes) {
            foreach ($potentialNewNotes as $note) {
                $folderEntityFromDb->addNote($note);
            }
        }

        $this->folderRepository->flush();

        return $folderEntityFromDb;
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

    public function show(int $id): ?Folder
    {
        $folders = $this->folderRepository->findBy(['id' => $id]);
        if (empty($folders)) {
            return null;
        }
        return $folders[0];
    }

    public function showBy(string $criteria, $argument): ?Folder
    {
        $folders = $this->folderRepository->findBy([$criteria, $argument]);
        if (empty($folders)) {
            return null;
        }
        return $folders[0];
    }
}