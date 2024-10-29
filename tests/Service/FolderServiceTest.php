<?php

namespace App\Tests\Service;

use App\Entity\Folder;
use App\Entity\Note;
use App\Repository\FolderRepository;
use App\Service\FolderService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class FolderServiceTest extends TestCase
{
    protected ?Folder $exampleFolder;

    protected ?MockObject $repoMock;

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
    }

    public function tearDown(): void
    {
        $this->exampleFolder->setId(null);
        $this->exampleFolder = null;
        $this->repoMock = null;
    }

    public function testUpdateFolder(): void
    {
        $this->repoMock
            ->method('findBy')->willReturn([$this->exampleFolder]);

        $folderService = new FolderService($this->repoMock);

        $newTitle = 'Folder 1 New Title';
        $firstNote = new Note();
        $firstNote->setTitle($newTitle . ' note 1');
        $firstNote->setContent('Hallo Welt 1');
        $newNotes = [$firstNote];

        $updatedFolder = $folderService->update($this->exampleFolder->getId(), $newTitle, $newNotes);

        $this->assertNotNull($updatedFolder);
        $this->assertEquals($newTitle, $updatedFolder->getTitle());
        $this->assertEquals($firstNote, $updatedFolder->getNotes()[0]);
    }

    public function testListFolders()
    {
        $secondFolderForList = new Folder();
        $title = 'Folder 2';
        $secondFolderForList->setId(2);
        $secondFolderForList->setTitle($title);

        $this->repoMock->method('findAll')->willReturn([$this->exampleFolder, $secondFolderForList]);

        $folderService = new FolderService($this->repoMock);
        $folders = $folderService->list();

        $this->assertCount(2, $folders);
        $this->assertNotNull($folders[0]);
        $this->assertNotNull($folders[1]);
        $this->assertEquals('Folder 1', $folders[0]->getTitle());
        $this->assertEquals($title, $folders[1]->getTitle());
    }

    public function testShowFolderByTitle()
    {
        $this->repoMock->method('findBy')->willReturn([$this->exampleFolder]);
        $folderService = new FolderService($this->repoMock);
        $folder = $folderService->showBy('title', $this->exampleFolder->getTitle());

        $this->assertNotNull($folder);
    }
}