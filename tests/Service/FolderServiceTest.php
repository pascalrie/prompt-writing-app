<?php

namespace App\Tests\Service;

use App\Entity\Folder;
use App\Entity\Note;
use App\Repository\FolderRepository;
use App\Service\FolderService;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FolderServiceTest extends TestCase
{
    protected ManagerRegistry $managerRegistry;

    protected ?Folder $exampleFolder;

    protected ?MockObject $repoMock;

    protected ?FolderService $folderService;

    public function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $title = 'Folder 1';

        $this->exampleFolder = new Folder();
        $this->exampleFolder->setId(1);
        $this->exampleFolder->setTitle($title);

        $this->repoMock = $this->getMockBuilder(FolderRepository::class)
            ->setConstructorArgs([$this->managerRegistry])
            ->onlyMethods(['add', 'persist', 'flush', 'findBy', 'findAll', 'remove'])
            ->getMock();

        $this->folderService = new FolderService($this->repoMock);
    }

    public function tearDown(): void
    {
        $this->exampleFolder = null;
        $this->folderService = null;
        $this->repoMock = null;
    }

    public function testUpdateTitleOfFolder(): void
    {
        $this->repoMock
            ->method('findBy')
            ->willReturn([$this->exampleFolder]);

        $newTitle = 'New Title';

        $updatedFolder = $this->folderService->update($this->exampleFolder->getId(), $newTitle);
        $this->assertNotNull($updatedFolder);
        $this->assertEquals($newTitle, $updatedFolder->getTitle());
    }

    public function testFolderUpdateNotes(): void
    {
        $this->repoMock
            ->method('findBy')->willReturn([$this->exampleFolder]);


        $firstNote = new Note();
        $firstNote->setContent('Hallo Welt 1');
        $newNotes = [$firstNote];

        $updatedFolder = $this->folderService->update($this->exampleFolder->getId(), "", $newNotes);

        $this->assertNotNull($updatedFolder);
        $this->assertNotNull($updatedFolder->getNotes());
        $this->assertEquals($firstNote, $updatedFolder->getNotes()[0]);
    }

    public function testListFolders()
    {
        $secondFolderForList = new Folder();
        $title = 'Folder 2';
        $secondFolderForList->setId(2);
        $secondFolderForList->setTitle($title);

        $this->repoMock->method('findAll')->willReturn([$this->exampleFolder, $secondFolderForList]);

        $folders = $this->folderService->list();

        $this->assertCount(2, $folders);
        $this->assertNotNull($folders[0]);
        $this->assertNotNull($folders[1]);
        $this->assertEquals('Folder 1', $folders[0]->getTitle());
        $this->assertEquals($title, $folders[1]->getTitle());
    }

    public function testShowNonExistentFolder(): void
    {
        $this->repoMock->method('findBy')->willReturn([]);
        $this->exampleFolder->setId(1000);
        $shouldBeNull = $this->folderService->show(1000);

        $this->assertNull($shouldBeNull);
    }

    public function testShowFolderByTitle()
    {
        $this->repoMock->method('findBy')->willReturn([$this->exampleFolder]);
        $folder = $this->folderService->showBy('title', $this->exampleFolder->getTitle());

        $this->assertNotNull($folder);
    }

    public function testNonExistentFolderByTitle(): void
    {
        $this->repoMock->method('findBy')->willReturn([]);
        $folderShouldBeNull = $this->folderService->showBy('title', 'non-existent-title');

        $this->assertNull($folderShouldBeNull);
    }
}