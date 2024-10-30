<?php

namespace App\Tests\Service;

use App\Entity\Note;
use App\Entity\Tag;
use App\Repository\NoteRepository;
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
            ->onlyMethods(['add', 'persist', 'flush', 'findBy', 'findAll'])
            ->getMock();

        $this->noteService = new NoteService($this->repoMock);
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
        $updatedNote = $this->noteService->update($this->exampleNote->getId(), "", false, $newTitle);

        $this->assertNotNull($updatedNote);
        $this->assertEquals($newTitle, $updatedNote->getTitle());
    }

    public function testNoteUpdateContentReplaceOld(): void
    {
        $newContent = 'New content for note';
        $this->repoMock->method('findBy')->willReturn([$this->exampleNote]);
        $updatedNote = $this->noteService->update($this->exampleNote->getId(), $newContent);

        $this->assertNotNull($updatedNote);
        $this->assertEquals($newContent, $updatedNote->getContent());
    }

    public function testNoteUpdateContentAddingTrue(): void
    {
        $newContent = '- New content for note';
        $resultContent = $this->exampleNote->getContent() . $newContent;
        $this->repoMock->method('findBy')->willReturn([$this->exampleNote]);
        $updatedNote = $this->noteService->update($this->exampleNote->getId(), $newContent, true);

        $this->assertNotNull($updatedNote);
        $this->assertNotNull($updatedNote->getContent());
        $this->assertEquals($resultContent, $updatedNote->getContent());
    }

    public function testNoteUpdateTags(): void
    {
        $tagToAdd = new Tag();
        $tagToAdd->setId(1);
        $tagToAdd->setTitle('Tag title');
        $tagToAdd->setColor('#FFFFFF');

        $this->repoMock->method('findBy')->willReturn([$this->exampleNote]);
        $updatedNote = $this->noteService->update($this->exampleNote->getId(), "",
            false, "", [$tagToAdd]);

        $this->assertNotNull($updatedNote);
    }
}