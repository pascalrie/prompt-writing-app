<?php

namespace App\Tests\Integration;

use App\Service\NoteService;
use Doctrine\ORM\EntityNotFoundException;

class NoteServiceIntegrationTest extends BaseIntegrationTest
{
    protected ?NoteService $noteService;

    protected function setUp(): void
    {
        self::bootKernel();
        parent::setUp();
        $this->noteService = static::getContainer()->get('App\Service\NoteService');
    }

    public function testNoteCreation(): void
    {
        $note = $this->noteService->create('Example Note');
        $this->assertNotNull($note->getId());
    }

    public function testShowExistingNoteFromDataFixture(): void
    {
        $existingNote = $this->noteService->showBy('title', 'First note');
        $this->assertNotNull($existingNote);
        $this->assertEquals('First note', $existingNote->getTitle());
    }

    public function testUpdateNoteTitle(): void
    {
        $note = $this->noteService->create('Old Title');
        $updatedNote = $this->noteService->update($note->getId(), 'New Title');
        $this->assertEquals('New Title', $updatedNote->getTitle());
    }

    public function testUpdateNoteContentReplace(): void
    {
        $note = $this->noteService->create('', 'Old Content');
        $updatedNote = $this->noteService->update($note->getId(), '', 'New Content');
        $this->assertEquals('New Content', $updatedNote->getContent());
    }

    public function testUpdateNoteContentAdd(): void
    {
        $note = $this->noteService->create('', 'Old Content');
        $updatedNote = $this->noteService->update($note->getId(), '', ' New Content', true, true);
        $this->assertEquals('Old Content New Content', $updatedNote->getContent());
    }

    public function testUpdateNoteAddTags(): void
    {
        $tags = ['#exampletag'];
        $note = $this->noteService->create('Title A');
        $updatedNote = $this->noteService->update($note->getId(), '', '', false, false, $tags);
        $this->assertEquals($tags[0], $updatedNote->getTags()[0]->getTitle());
    }

    public function testUpdateNoteWithNonExistingTag(): void
    {
        $tags = ['#hallowelt'];
        $note = $this->noteService->create('Title A');
        $updatedNote = $this->noteService->update($note->getId(), '', '', false, false, $tags);
        $this->assertNull($updatedNote->getTags()[0]);
    }

    public function testUpdateNoteCategoryTitle(): void
    {
        $note = $this->noteService->create('Title A');
        $updatedNote = $this->noteService->update($note->getId(), '', '', false, false, [], 'Category 1');
        $this->assertEquals('Category 1', $updatedNote->getCategory()->getTitle());
    }

    /**
     * @throws EntityNotFoundException
     */
    public function testDeleteNote(): void
    {
        $note = $this->noteService->create('To Be Deleted');
        $noteId = $note->getId();
        $this->noteService->delete($note->getId());
        $shouldBeNull = $this->noteService->show($noteId);
        $this->assertNull($shouldBeNull);
    }

    public function testListNotes(): void
    {
        $this->noteService->create('Note A');
        $this->noteService->create('Note B');
        $notes = $this->noteService->list();
        $this->assertCount(3, $notes); // Including the fixture
    }

    public function testShowNoteById(): void
    {
        $note = $this->noteService->create('Specific Note');
        $fetchedNote = $this->noteService->show($note->getId());
        $this->assertNotNull($fetchedNote);
        $this->assertEquals('Specific Note', $fetchedNote->getTitle());
    }
}