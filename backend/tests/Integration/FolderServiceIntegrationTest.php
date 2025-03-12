<?php

namespace App\Tests\Integration;

use App\Service\FolderService;
use App\Service\NoteService;
use App\Tests\Integration\BaseIntegrationTest;
use Doctrine\ORM\EntityNotFoundException;

class FolderServiceIntegrationTest extends BaseIntegrationTest
{
    protected ?FolderService $folderService;

    protected ?NoteService $noteService;

    protected function setUp(): void
    {
        self::bootKernel();
        parent::setUp();
        $this->folderService = static::getContainer()->get('App\Service\FolderService');
        $this->noteService = static::getContainer()->get('App\Service\NoteService');
    }

    public function testFolderCreation(): void
    {
        $folder = $this->folderService->create('Example Folder');
        $this->assertNotNull($folder->getId());
    }

    public function testShowExistingFolderFromDataFixture(): void
    {
        $existingFolder = $this->folderService->showBy('title', 'Folder 1');
        $this->assertNotNull($existingFolder);
        $this->assertEquals('Folder 1', $existingFolder->getTitle());
    }

    public function testUpdateFolderTitle(): void
    {
        $folder = $this->folderService->create('Old Title');
        $updatedFolder = $this->folderService->update($folder->getId(), 'New Title');
        $this->assertEquals('New Title', $updatedFolder->getTitle());
    }

    /**
     * @throws EntityNotFoundException
     */
    public function testUpdateFolderAddNotes()
    {
        $folder = $this->folderService->create('Old Title');
        $noteOne = $this->noteService->create('Note A');
        $noteTwo = $this->noteService->create('Note B');
        $updatedFolder = $this->folderService->update($folder->getId(), '', [$noteOne, $noteTwo], false);
        $this->assertCount(2, $updatedFolder->getNotes());
    }

    public function testUpdateFolderReplaceNote()
    {
        $folder = $this->folderService->create('Old Title');
        $noteOne = $this->noteService->create('Note A');
        $noteTwo = $this->noteService->create('Note B');
        //for the first note
        $this->folderService->update($folder->getId(), '', [$noteOne], false);

        $updatedFolder = $this->folderService->update($folder->getId(), '', [$noteTwo], true);

        $this->assertCount(1, $updatedFolder->getNotes());
        $this->assertEquals($noteTwo->getTitle(), $updatedFolder->getNotes()[1]->getTitle());
    }

    /**
     * @throws EntityNotFoundException
     */
    public function testDeleteFolder(): void
    {
        $folder = $this->folderService->create('To Be Deleted');
        $id = $folder->getId();
        $this->folderService->delete($folder->getId());
        $folder = $this->folderService->show($id);
        $this->assertNull($folder);
    }

    public function testListFolders(): void
    {
        $this->folderService->create('Folder A');
        $this->folderService->create('Folder B');
        $folders = $this->folderService->list();
        $this->assertCount(3, $folders); // Including the fixture
    }

    public function testShowFolderById(): void
    {
        $folder = $this->folderService->create('Specific Folder');
        $fetchedFolder = $this->folderService->show($folder->getId());
        $this->assertNotNull($fetchedFolder);
        $this->assertEquals('Specific Folder', $fetchedFolder->getTitle());
    }
}