<?php

namespace App\Tests\Integration;

use App\Service\NoteService;
use App\Service\TagService;

class TagServiceIntegrationTest extends BaseIntegrationTest
{
    protected ?TagService $tagService;

    protected ?NoteService $noteService;

    protected function setUp(): void
    {
        self::bootKernel();
        parent::setUp();
        $this->tagService = static::getContainer()->get(TagService::class);
        $this->noteService = static::getContainer()->get(NoteService::class);
    }

    public function testListTags(): void
    {
        $tag = $this->tagService->create('#tagabctest');
        $tags = $this->tagService->list();
        $this->assertCount(2, $tags); // with the fixture tag
    }

    public function testTagCreationWithTitle(): void
    {
        $tag = $this->tagService->create('#tagabctest');

        $tagFromDb = $this->tagService->showOneBy('title', '#tagabctest');
        $this->assertNotNull($tagFromDb);
        $this->assertEquals($tag->getTitle(), $tagFromDb->getTitle());
    }

    public function testTagCreationWithTitleAndColor(): void
    {
        $tag = $this->tagService->create('#tagabctest', null, '#000000');

        $tagFromDb = $this->tagService->showOneBy('title', '#tagabctest');
        $this->assertNotNull($tagFromDb);
        $this->assertEquals($tag->getTitle(), $tagFromDb->getTitle());
        $this->assertEquals($tag->getColor(), $tagFromDb->getColor());
    }

    public function testTagCreationWithNotes(): void
    {
        $noteOne = $this->noteService->create('Note A');
        $noteTwo = $this->noteService->create('Note B');
        $tag = $this->tagService->create('#tagabctest', [$noteOne, $noteTwo], '#001000');

        $tagFromDb = $this->tagService->showOneBy('title', '#tagabctest');
        $this->assertNotNull($tagFromDb);
        $this->assertEquals($tag->getTitle(), $tagFromDb->getTitle());
        $this->assertEquals($tag->getColor(), $tagFromDb->getColor());
    }

    public function testShowExistingTagFromDataFixture(): void
    {
        $tagFromDb = $this->tagService->showOneBy('title', '#exampletag');
        $this->assertNotNull($tagFromDb);
        $this->assertEquals('#exampletag', $tagFromDb->getTitle());
    }

    public function testUpdateTagTitle(): void
    {
        $tag = $this->tagService->showOneBy('title', '#exampletag');
        $updatedTag = $this->tagService->update($tag->getId(), '#newtitle');
        $this->assertEquals('#newtitle', $updatedTag->getTitle());
    }

    public function testUpdateTagNotes(): void
    {
        $tag = $this->tagService->showOneBy('title', '#exampletag');
        $noteOne = $this->noteService->create('Note A');
        $noteTwo = $this->noteService->create('Note B');
        $updatedTag = $this->tagService->update($tag->getId(), '', [$noteOne, $noteTwo]);
        $this->assertCount(3, $updatedTag->getNotes()); // 1 from fixture
    }

    public function testUpdateColorOfTag(): void
    {
        $tag = $this->tagService->showOneBy('title', '#exampletag');
        $updatedTag = $this->tagService->update($tag->getId(), "", [], "#128918");
        $this->assertEquals("#128918", $updatedTag->getColor());
    }

    public function testDeleteTag(): void
    {
        $tag = $this->tagService->showOneBy('title', '#exampletag');
        $this->tagService->delete($tag->getId());
        $tag = $this->tagService->showOneBy('title', '#exampletag');
        $this->assertNull($tag);
    }

    public function testShowTagById(): void
    {
        $tag = $this->tagService->showOneBy('title', '#exampletag');
        $this->assertNotNull($tag);
        $this->assertEquals('#exampletag', $tag->getTitle());
    }
}