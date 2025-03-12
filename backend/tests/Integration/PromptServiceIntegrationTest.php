<?php

namespace App\Tests\Integration;

use App\Service\CategoryService;
use App\Service\NoteService;
use App\Service\PromptService;

class PromptServiceIntegrationTest extends BaseIntegrationTest
{
    protected ?PromptService $promptService;

    protected ?CategoryService $categoryService;

    protected ?NoteService $noteService;

    protected function setUp(): void
    {
        self::bootKernel();
        parent::setUp();
        $this->promptService = static::getContainer()->get(PromptService::class);
        $this->categoryService = static::getContainer()->get(CategoryService::class);
        $this->noteService = static::getContainer()->get(NoteService::class);
    }

    public function testListPrompts(): void
    {
        $category = $this->categoryService->create('Category A');
        $this->promptService->create('Prompt A', $category->getId());
        $this->promptService->create('Prompt B', $category->getId());
        $prompts = $this->promptService->list();
        $this->assertCount(3, $prompts); // Including the fixture
    }

    public function testPromptCreation(): void
    {
        $category = $this->categoryService->create('Category A');
        $prompt = $this->promptService->create('Example Prompt', $category->getId());
        $this->assertNotNull($prompt->getId());
    }

    public function testShowExistingPromptFromDataFixture(): void
    {
        $title = 'Prompt 1?';
        $existingPrompt = $this->promptService->showBy('title', $title);
        $this->assertNotNull($existingPrompt);
        $this->assertEquals($title, $existingPrompt->getTitle());
    }

    public function testUpdatePromptTitle(): void
    {
        $category = $this->categoryService->create('Category A');
        $prompt = $this->promptService->create('Old Title', $category->getId());

        $this->assertNotNull($prompt);

        $updatedPrompt = $this->promptService->update($prompt->getId(), 'New Title');
        $this->assertEquals('New Title', $updatedPrompt->getTitle());
    }

    public function testUpdateCategoryOfPrompt(): void
    {
        $category = $this->categoryService->create('Category A');
        $prompt = $this->promptService->create('Old Title', $category->getId());

        $this->assertNotNull($prompt);
        $updatedPrompt = $this->promptService->update($prompt->getId(), '', $category);
        $this->assertEquals($category->getId(), $updatedPrompt->getCategory()->getId());
    }

    public function testAddNoteToPrompt(): void
    {
        $prompt = $this->promptService->showBy('title', 'Prompt 1?');
        $this->assertNotNull($prompt);
        $note = $this->noteService->create('Note A');
        $updatedPrompt = $this->promptService->update($prompt->getId(), '', null, [$note]);
        $this->assertEquals($note, $updatedPrompt->getNotes()[1]);
    }

    public function testDeletePrompt(): void
    {
        $prompt = $this->promptService->showBy('title', 'Prompt 1?');
        $this->assertNotNull($prompt);
        $this->promptService->delete($prompt->getId());
        $prompt = $this->promptService->showBy('title', 'Prompt 1?');
        $this->assertNull($prompt);
    }

    public function testShowPromptById(): void
    {
        $prompt = $this->promptService->showBy('title', 'Prompt 1?');
        $this->assertNotNull($prompt);
        $this->assertEquals('Prompt 1?', $prompt->getTitle());
    }

    /**
     * @throws \Exception
     */
    public function testShowRandomPrompt(): void
    {
        $prompt = $this->promptService->showRandom();
        $this->assertNotNull($prompt);
    }
}