<?php

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Tag;
use App\Repository\CategoryRepository;
use App\Repository\NoteRepository;
use App\Repository\TagRepository;
use App\Service\NoteService;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NoteServiceTest extends TestCase
{
    protected ManagerRegistry $managerRegistry;

    protected ?Note $exampleNote;

    protected ?NoteService $noteService;

    protected ?MockObject $repoMock;

    public function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);

        $this->exampleNote = new Note();
        $this->exampleNote->setId(1);
        $this->exampleNote->setTitle('Note title');
        $this->exampleNote->setContent('Note content');

        $this->repoMock = $this->getMockBuilder(NoteRepository::class)
            ->setConstructorArgs([$this->managerRegistry])
            ->onlyMethods(['add', 'persist', 'flush', 'findBy', 'findAll', 'findOneBy'])
            ->getMock();

        $this->repoMockTag = $this->getMockBuilder(TagRepository::class)
            ->setConstructorArgs([$this->managerRegistry])
            ->onlyMethods(['add', 'persist', 'flush', 'findBy', 'findAll', 'findOneBy'])
            ->getMock();

        $this->repoMockCategory = $this->getMockBuilder(CategoryRepository::class)
            ->setConstructorArgs([$this->managerRegistry])
            ->onlyMethods(['add', 'persist', 'flush', 'findBy', 'findAll', 'findOneBy'])
            ->getMock();

        $this->noteService = new NoteService($this->repoMock, $this->repoMockTag, $this->repoMockCategory);
    }

    public function tearDown(): void
    {
        $this->exampleNote = null;
        $this->repoMock = null;
        $this->noteService = null;
    }

    public function testNoteUpdateTitle(): void
    {
        $newTitle = 'New title for note';
        $this->repoMock->method('findBy')->willReturn([$this->exampleNote]);
        $updatedNote = $this->noteService->update($this->exampleNote->getId(), $newTitle, "", false);

        $this->assertNotNull($updatedNote);
        $this->assertEquals($newTitle, $updatedNote->getTitle());
    }

    public function testNoteUpdateContentReplaceOld(): void
    {
        $newContent = 'New content for note';
        $this->repoMock->method('findBy')->willReturn([$this->exampleNote]);
        $updatedNote = $this->noteService->update($this->exampleNote->getId(), "", $newContent);

        $this->assertNotNull($updatedNote);
        $this->assertEquals($newContent, $updatedNote->getContent());
    }

    public function testNoteUpdateContentAddingTrue(): void
    {
        $newContent = '- New content for note';
        $resultContent = $this->exampleNote->getContent() . $newContent;
        $this->repoMock->method('findBy')->willReturn([$this->exampleNote]);
        $updatedNote = $this->noteService->update($this->exampleNote->getId(), "", $newContent, true);

        $this->assertNotNull($updatedNote);
        $this->assertNotNull($updatedNote->getContent());
        $this->assertEquals($resultContent, $updatedNote->getContent());
    }

    public function testNoteUpdateAddTag(): void
    {
        $tagToAdd = new Tag();
        $tagToAdd->setId(1);
        $tagToAdd->setTitle('Tag title 23');
        $tagToAdd->setColor('#FFFFFF');

        $this->repoMock->method('findBy')->willReturn([$this->exampleNote]);
        $this->repoMockTag->method('findBy')->willReturn([$tagToAdd]);
        $updatedNote = $this->noteService->update($this->exampleNote->getId(), "",
            "", false, false, [$tagToAdd]);

        $this->assertNotNull($updatedNote);
        $this->assertNotNull($updatedNote->getTags());
        $this->assertEquals($tagToAdd, $updatedNote->getTags()[0]);
    }

    public function testUpdateNoteAddCategory(): void
    {
        $categoryToAdd = new Category();
        $categoryToAdd->setId(1);
        $categoryToAdd->setTitle('Exciting Category title');

        $this->repoMock->method('findBy')->willReturn([$this->exampleNote]);
        $this->repoMockCategory->method('findBy')->willReturn([$categoryToAdd]);
        $updatedNote = $this->noteService->update($this->exampleNote->getId(), "", "", false,
            false, [], $categoryToAdd->getTitle());

        $this->assertNotNull($updatedNote);
        $this->assertNotNull($updatedNote->getCategory());
        $this->assertEquals($categoryToAdd, $updatedNote->getCategory());
    }

    public function testUpdateNoteRemoveContent(): void
    {
        $this->repoMock
            ->method('findBy')->willReturn([$this->exampleNote]);

        $this->exampleNote = $this->noteService->update($this->exampleNote->getId(), "", "", false, true);

        $this->assertNotNull($this->exampleNote);
        $this->assertEquals("", $this->exampleNote->getContent());
        $this->assertNotEquals("", $this->exampleNote->getTitle());
    }


    public function testShowNoteWithImpossibleId(): void
    {
        $this->repoMock->method('findBy')->willReturn([]);
        $this->exampleNote->setId(100);
        $shownNoteHopefullyNull = $this->noteService->show($this->exampleNote->getId());

        $this->assertNull($shownNoteHopefullyNull);
    }

    public function testShowNote(): void
    {
        $this->repoMock->method('findBy')->willReturn([$this->exampleNote]);
        $shownNote = $this->noteService->show($this->exampleNote->getId());

        $this->assertNotNull($shownNote);
        $this->assertEquals($this->exampleNote, $shownNote);
    }

    public function testShowNoteByTitleNotFound(): void
    {
        $this->repoMock->method('findBy')->willReturn([]);
        $noteShouldBeNull = $this->noteService->showBy('title', 'impossible title');

        $this->assertNull($noteShouldBeNull);
    }

    public function testShowNotesByTitle(): void
    {
        $this->repoMock->method('findBy')->willReturn([$this->exampleNote]);
        $shownNote = $this->noteService->showBy('title', $this->exampleNote->getTitle());

        $this->assertNotNull($shownNote);
        $this->assertEquals($this->exampleNote, $shownNote);
    }
}