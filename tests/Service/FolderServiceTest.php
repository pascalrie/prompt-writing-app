<?php

namespace App\Tests\Service;

use App\Entity\Folder;
use App\Entity\Note;
use App\Repository\FolderRepository;
use App\Service\FolderService;
use Doctrine\ORM\EntityNotFoundException;
use PHPUnit\Framework\TestCase;

class FolderServiceTest extends TestCase
{
    private FolderService $folderService;
    private FolderRepository $folderRepository;

    protected function setUp(): void
    {
        // Create a mock for the FolderRepository
        $this->folderRepository = $this->createMock(FolderRepository::class);

        // Initialize the FolderService with the mocked repository
        $this->folderService = new FolderService($this->folderRepository);
    }

    public function testCreateFolderWithoutNote(): void
    {
        $folderTitle = 'New Folder';

        // Expect the `add` method to be called once with the folder and flush set to true
        $this->folderRepository->expects($this->once())
            ->method('add')
            ->with(
                $this->callback(function (Folder $folder) use ($folderTitle) {
                    return $folder->getTitle() === $folderTitle && $folder->getNotes()->isEmpty();
                }),
                true
            );

        $folder = $this->folderService->create($folderTitle);

        // Assert that the returned folder has the correct title and no notes
        $this->assertInstanceOf(Folder::class, $folder);
        $this->assertEquals($folderTitle, $folder->getTitle());
        $this->assertEmpty($folder->getNotes());
    }

    public function testCreateFolderWithNote(): void
    {
        $folderTitle = 'New Folder';
        $note = new Note();
        $note->setContent('Hello World');

        // Expect the `add` method to be called once with the correct folder and flush set to true
        $this->folderRepository->expects($this->once())
            ->method('add')
            ->with(
                $this->callback(function (Folder $folder) use ($folderTitle, $note) {
                    return $folder->getTitle() === $folderTitle && $folder->getNotes()->contains($note);
                }),
                true
            );

        $folder = $this->folderService->create($folderTitle, $note);

        // Assert that the returned folder has the correct title and contains the note
        $this->assertInstanceOf(Folder::class, $folder);
        $this->assertEquals($folderTitle, $folder->getTitle());
        $this->assertTrue($folder->getNotes()->contains($note));
    }

    public function testUpdateFolderTitle(): void
    {
        $existingFolder = new Folder();
        $existingFolder->setId(1);
        $existingFolder->setTitle('Old Title');

        $newFolderTitle = 'Updated Title';

        // Mock the `find` method to return the existing folder
        $this->folderRepository->expects($this->once())
            ->method('find')
            ->with($existingFolder->getId())
            ->willReturn($existingFolder);

        // Expect the `add` method to save the updated folder
        $this->folderRepository->expects($this->once())
            ->method('add')
            ->with($existingFolder, true);

        $updatedFolder = $this->folderService->update($existingFolder->getId(), $newFolderTitle);

        // Assert that the returned folder has the updated title
        $this->assertEquals($newFolderTitle, $updatedFolder->getTitle());
    }

    public function testUpdateFolderNotesReplace(): void
    {
        $existingFolder = new Folder();
        $existingFolder->setId(1);
        $note1 = new Note();
        $note1->setContent('Old Note');
        $existingFolder->addNote($note1);

        $newNote = new Note();
        $newNote->setContent('New Note');

        // Mock the `find` method to return the existing folder
        $this->folderRepository->expects($this->once())
            ->method('find')
            ->with($existingFolder->getId())
            ->willReturn($existingFolder);

        // Expect the `add` method to save the updated folder
        $this->folderRepository->expects($this->once())
            ->method('add')
            ->with($existingFolder, true);

        $updatedFolder = $this->folderService->update($existingFolder->getId(), '', [$newNote], true);

        // Assert that the folder's notes were replaced
        $this->assertCount(1, $updatedFolder->getNotes());
        $this->assertTrue($updatedFolder->getNotes()->contains($newNote));
        $this->assertFalse($updatedFolder->getNotes()->contains($note1));
    }

    public function testUpdateFolderNotesAppend(): void
    {
        $existingFolder = new Folder();
        $existingFolder->setId(1);
        $note1 = new Note();
        $note1->setContent('Existing Note');
        $existingFolder->addNote($note1);

        $newNote = new Note();
        $newNote->setContent('New Note');

        // Mock the `find` method to return the existing folder
        $this->folderRepository->expects($this->once())
            ->method('find')
            ->with($existingFolder->getId())
            ->willReturn($existingFolder);

        // Expect the `add` method to save the updated folder
        $this->folderRepository->expects($this->once())
            ->method('add')
            ->with($existingFolder, true);

        $updatedFolder = $this->folderService->update($existingFolder->getId(), '', [$newNote]);

        // Assert that the folder's notes were appended
        $this->assertCount(2, $updatedFolder->getNotes());
        $this->assertTrue($updatedFolder->getNotes()->contains($newNote));
        $this->assertTrue($updatedFolder->getNotes()->contains($note1));
    }

    public function testDeleteFolder(): void
    {
        $existingFolder = new Folder();
        $existingFolder->setId(1);

        // Mock the `find` method to return the folder
        $this->folderRepository->expects($this->once())
            ->method('find')
            ->with($existingFolder->getId())
            ->willReturn($existingFolder);

        // Expect the `remove` method to delete the folder
        $this->folderRepository->expects($this->once())
            ->method('remove')
            ->with($existingFolder, true);

        $this->folderService->delete($existingFolder->getId());
    }

    public function testDeleteFolderNotFound(): void
    {
        // Mock the `find` method to return null
        $this->folderRepository->expects($this->once())
            ->method('find')
            ->willReturn(null);

        $this->expectException(EntityNotFoundException::class);
        $this->folderService->delete(999);
    }

    public function testListFolders(): void
    {
        $folder1 = new Folder();
        $folder2 = new Folder();

        // Mock the `findAll` method to return a list of folders
        $this->folderRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$folder1, $folder2]);

        $folders = $this->folderService->list();

        // Assert that two folders are returned
        $this->assertCount(2, $folders);
        $this->assertContainsOnlyInstancesOf(Folder::class, $folders);
    }

    public function testShowFolderById(): void
    {
        $folder = new Folder();

        // Mock the `findOneBy` method to return the folder
        $this->folderRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 1])
            ->willReturn($folder);

        $result = $this->folderService->show(1);

        $this->assertEquals($folder, $result);
    }

    public function testShowFolderByCriteria(): void
    {
        $folder = new Folder();

        // Mock the `findOneBy` method to return the folder
        $this->folderRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['title' => 'Test Folder'])
            ->willReturn($folder);

        $result = $this->folderService->showBy('title', 'Test Folder');

        $this->assertEquals($folder, $result);
    }
}