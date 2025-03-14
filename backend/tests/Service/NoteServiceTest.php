<?php

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Tag;
use App\Repository\CategoryRepository;
use App\Repository\NoteRepository;
use App\Repository\PromptRepository;
use App\Repository\TagRepository;
use App\Service\NoteService;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NoteServiceTest extends TestCase
{
    protected ManagerRegistry $managerRegistry;

    protected ?Note $exampleNote;

    protected ?NoteService $noteService;

    protected ?MockObject $repoMockNote;

    protected ?MockObject $repoMockTag;

    protected ?MockObject $repoMockCategory;

    protected ?MockObject $repoMockPrompt;

    public function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);

        $this->exampleNote = new Note();
        $this->exampleNote->setId(1);
        $this->exampleNote->setTitle('Note title');
        $this->exampleNote->setContent('Note content');

        $this->repoMockNote = $this->getMockBuilder(NoteRepository::class)
            ->setConstructorArgs([$this->managerRegistry, $this->createMock(\Doctrine\ORM\EntityManager::class)])
            ->onlyMethods(['add', 'persist', 'flush', 'findBy', 'findAll', 'findOneBy'])
            ->getMock();

        $this->repoMockTag = $this->getMockBuilder(TagRepository::class)
            ->setConstructorArgs([$this->managerRegistry, $this->createMock(\Doctrine\ORM\EntityManager::class)])
            ->onlyMethods(['add', 'persist', 'flush', 'findBy', 'findAll', 'findOneBy'])
            ->getMock();

        $this->repoMockPrompt = $this->getMockBuilder(PromptRepository::class)
            ->setConstructorArgs([$this->managerRegistry, $this->createMock(\Doctrine\ORM\EntityManager::class)])
            ->onlyMethods(['add', 'persist', 'flush', 'findBy', 'findAll', 'findOneBy'])
            ->getMock();

        $this->repoMockCategory = $this->getMockBuilder(CategoryRepository::class)
            ->setConstructorArgs([$this->managerRegistry, $this->createMock(\Doctrine\ORM\EntityManager::class)])
            ->onlyMethods(['add', 'persist', 'flush', 'findBy', 'findAll', 'findOneBy'])
            ->getMock();

        $this->noteService = new NoteService($this->repoMockNote, $this->repoMockTag, $this->repoMockCategory, $this->repoMockPrompt);
    }

    public function tearDown(): void
    {
        $this->exampleNote = null;
        $this->repoMockNote = null;
        $this->noteService = null;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function testNoteUpdateTitle(): void
    {
        $newTitle = 'New title for note';
        $this->repoMockNote->method('findOneBy')->willReturn($this->exampleNote);
        $updatedNote = $this->noteService->update($this->exampleNote->getId(), $newTitle, "", false);

        $this->assertNotNull($updatedNote);
        $this->assertEquals($newTitle, $updatedNote->getTitle());
    }

    /**
     * @throws EntityNotFoundException
     */
    public function testNoteUpdateContentReplaceOld(): void
    {
        $newContent = 'New content for note';
        $this->repoMockNote->method('findOneBy')->willReturn($this->exampleNote);
        $updatedNote = $this->noteService->update($this->exampleNote->getId(), "", $newContent);

        $this->assertNotNull($updatedNote);
        $this->assertEquals($newContent, $updatedNote->getContent());
    }

    /**
     * @throws EntityNotFoundException
     */
    public function testNoteUpdateContentAddingTrue(): void
    {
        $newContent = '- New content for note';
        $resultContent = $this->exampleNote->getContent() . $newContent;
        $this->repoMockNote->method('findOneBy')->willReturn($this->exampleNote);
        $updatedNote = $this->noteService->update($this->exampleNote->getId(), "", $newContent, true);

        $this->assertNotNull($updatedNote);
        $this->assertNotNull($updatedNote->getContent());
        $this->assertEquals($resultContent, $updatedNote->getContent());
    }

    /**
     * @throws EntityNotFoundException
     */
    public function testNoteUpdateAddTag(): void
    {
        $tagToAdd = new Tag();
        $tagToAdd->setId(1);
        $tagToAdd->setTitle('Tag title 23');
        $tagToAdd->setColor('#FFFFFF');

        $this->repoMockNote->method('findOneBy')->willReturn($this->exampleNote);
        $this->repoMockTag->method('findOneBy')->willReturn($tagToAdd);
        $updatedNote = $this->noteService->update($this->exampleNote->getId(), "",
            "", false, false, [$tagToAdd]);

        $this->assertNotNull($updatedNote);
        $this->assertNotNull($updatedNote->getTags());
        $this->assertEquals($tagToAdd, $updatedNote->getTags()[0]);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function testUpdateNoteAddCategory(): void
    {
        $categoryToAdd = new Category();
        $categoryToAdd->setId(1);
        $categoryToAdd->setTitle('Exciting Category title');

        $this->repoMockNote->method('findOneBy')->willReturn($this->exampleNote);
        $this->repoMockCategory->method('findOneBy')->willReturn($categoryToAdd);
        $updatedNote = $this->noteService->update($this->exampleNote->getId(), "", "", false,
            false, [], $categoryToAdd->getTitle());

        $this->assertNotNull($updatedNote);
        $this->assertNotNull($updatedNote->getCategory());
        $this->assertEquals($categoryToAdd, $updatedNote->getCategory());
    }

    /**
     * @throws EntityNotFoundException
     */
    public function testUpdateNoteRemoveContent(): void
    {
        $this->repoMockNote
            ->method('findOneBy')->willReturn($this->exampleNote);

        $this->exampleNote = $this->noteService->update($this->exampleNote->getId(), "", "", false, true);

        $this->assertNotNull($this->exampleNote);
        $this->assertEquals("", $this->exampleNote->getContent());
        $this->assertNotEquals("", $this->exampleNote->getTitle());
    }


    public function testShowNoteWithImpossibleId(): void
    {
        $this->repoMockNote->method('findOneBy')->willReturn(null);
        $this->exampleNote->setId(100);
        $shownNoteHopefullyNull = $this->noteService->show($this->exampleNote->getId());

        $this->assertNull($shownNoteHopefullyNull);
    }

    public function testShowNote(): void
    {
        $this->repoMockNote->method('findOneBy')->willReturn($this->exampleNote);
        $shownNote = $this->noteService->show($this->exampleNote->getId());

        $this->assertNotNull($shownNote);
        $this->assertEquals($this->exampleNote, $shownNote);
    }

    public function testShowNoteByTitleNotFound(): void
    {
        $this->repoMockNote->method('findOneBy')->willReturn(null);
        $noteShouldBeNull = $this->noteService->showBy('title', 'impossible title');

        $this->assertNull($noteShouldBeNull);
    }

    public function testShowNotesByTitle(): void
    {
        $this->repoMockNote->method('findOneBy')->willReturn($this->exampleNote);
        $shownNote = $this->noteService->showBy('title', $this->exampleNote->getTitle());

        $this->assertNotNull($shownNote);
        $this->assertEquals($this->exampleNote, $shownNote);
    }
}