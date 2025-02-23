<?php

namespace App\Tests\Service;

use App\Entity\Note;
use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Service\TagService;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TagServiceTest extends TestCase
{
    protected ManagerRegistry $managerRegistry;

    protected ?Tag $exampleTag;

    protected ?TagService $tagService;

    protected ?MockObject $repoMock;

    public function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $title = 'Example Tag Title';
        $color = '#000000';

        $this->exampleTag = new Tag();
        $this->exampleTag->setId(1);
        $this->exampleTag->setTitle($title);
        $this->exampleTag->setColor($color);

        $exampleNote = new Note();
        $exampleNote->setId(1);
        $exampleNote->setTitle('Note title');
        $exampleNote->addTag($this->exampleTag);

        $this->exampleTag->addNote($exampleNote);

        $this->repoMock = $this->getMockBuilder(TagRepository::class)
            ->setConstructorArgs([$this->managerRegistry])
            ->onlyMethods(['add', 'persist', 'flush', 'findBy', 'findAll', 'findOneBy'])
            ->getMock();

        $this->tagService = new TagService($this->repoMock);
    }

    public function tearDown(): void
    {
        $this->tagService = null;
        $this->repoMock = null;
        $this->exampleTag = null;
    }

    public function testUpdateTitle(): void
    {
        $newTitle = 'New Title of tag';
        $this->repoMock->method('findBy')->willReturn([$this->exampleTag]);
        $updatedTag = $this->tagService->update($this->exampleTag->getId(), $newTitle);

        $this->assertNotNull($updatedTag);
        $this->assertEquals($newTitle, $updatedTag->getTitle());
    }

    public function testUpdateColor(): void
    {
        $newColor = '#111111';
        $this->repoMock->method('findBy')->willReturn([$this->exampleTag]);
        $updatedTag = $this->tagService->update($this->exampleTag->getId(), "", [], $newColor);

        $this->assertNotNull($updatedTag);
        $this->assertEquals($newColor, $updatedTag->getColor());
    }

    public function testUpdateAddNote(): void
    {
        $newNote = new Note();
        $newNote->setId(2);
        $newNote->setTitle('2 Note title');
        $newNote->setContent('2 Note content');

        $this->repoMock->method('findBy')->willReturn([$this->exampleTag]);
        $updatedTag = $this->tagService->update($this->exampleTag->getId(), '', [$newNote]);

        $this->assertNotNull($updatedTag);
        // IMPORTANT: index + 1 ($updatedTag->getNotes()), because of initial note from setUp()
        $this->assertEquals($newNote->getTitle(), $updatedTag->getNotes()[1]->getTitle());
        $this->assertEquals($newNote->getContent(), $updatedTag->getNotes()[1]->getContent());
        $this->assertEquals(2, $updatedTag->getNotes()[1]->getId());
    }
}