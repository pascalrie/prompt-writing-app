<?php

namespace App\Service;

use App\Entity\Folder;
use App\Entity\Note;
use App\Repository\FolderRepository;
use App\Service\Factory\IService;

class FolderService implements IService
{
    protected FolderRepository $folderRepository;

    /**
     * @param FolderRepository $folderRepository
     */
    public function __construct(FolderRepository $folderRepository)
    {
        $this->folderRepository = $folderRepository;
    }

    /**
     * @param string $title
     * @param Note|null $firstNote
     * @return Folder
     */
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

    /**
     * @param int $folderId
     * @param string $newTitle
     * @param array|null $potentialNewNotes
     * @return Folder
     */
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

    /**
     * @return array
     */
    public function list(): array
    {
        return $this->folderRepository->findAll();
    }

    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $category = $this->folderRepository->findBy(['id' => $id])[0];
        $this->folderRepository->remove($category);
    }

    /**
     * @param int $id
     * @return Folder|null
     */
    public function show(int $id): ?Folder
    {
        $folders = $this->folderRepository->findBy(['id' => $id]);
        if (empty($folders)) {
            return null;
        }
        return $folders[0];
    }

    /**
     * @param string $criteria
     * @param $argument
     * @return Folder|null
     */
    public function showBy(string $criteria, $argument): ?Folder
    {
        $folders = $this->folderRepository->findBy([$criteria, $argument]);
        if (empty($folders)) {
            return null;
        }
        return $folders[0];
    }
}