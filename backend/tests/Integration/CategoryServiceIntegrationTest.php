<?php

namespace App\Tests\Integration;

use App\DataFixtures\CategoryFixtures;
use App\Service\CategoryService;
use App\Service\NoteService;
use App\Service\PromptService;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CategoryServiceIntegrationTest extends BaseIntegrationTest
{
    protected ?CategoryService $categoryService;

    protected ?PromptService $promptService;

    protected ?NoteService $noteService;

    protected function setUp(): void
    {
        self::bootKernel();
        parent::setUp();
        $this->promptService = static::getContainer()->get('App\Service\PromptService');
        $this->categoryService = static::getContainer()->get('App\Service\CategoryService');
        $this->noteService = static::getContainer()->get('App\Service\NoteService');
    }

    public function testCategoryCreation(): void
    {
        $category = $this->categoryService->create('Example Category');

        $this->assertNotNull($category->getId());
    }

    public function testShowExistingCategoryFromDataFixture(): void
    {
        $existingCategory = $this->categoryService->showByTitle('Category 1');

        $this->assertNotNull($existingCategory);
        $this->assertEquals('Category 1', $existingCategory->getTitle());
    }

    public function testUpdateCategoryTitle(): void
    {
        $category = $this->categoryService->create('Old Title');
        $updatedCategory = $this->categoryService->update($category->getId(), 'New Title');

        $this->assertEquals('New Title', $updatedCategory->getTitle());
    }

    public function testUpdateCategoryAddPrompt(): void
    {
        $category = $this->categoryService->showByTitle('Category 1');
        $prompt = $this->promptService->create('Prompt B?', $category->getId());
        $prompts = [$prompt];
        $category = $this->categoryService->update($category->getId(), '', [], $prompts);

        $this->assertEquals('Prompt B?', $category->getPrompts()[1]->getTitle()); // because of fixtures
    }

    public function testUpdateCategoryAddNote(): void
    {
        $category = $this->categoryService->showByTitle('Category 1');
        $note = $this->noteService->create('Second note');
        $category = $this->categoryService->update($category->getId(), '', [$note]);

        $this->assertCount(2, $category->getNotes());
    }

    public function testDeleteCategory(): void
    {
        $category = $this->categoryService->create('To Be Deleted');
        $this->categoryService->delete($category->getId());
        $category = $this->categoryService->showByTitle('To Be Deleted');

        $this->assertNull($category);
    }

    public function testListCategories(): void
    {
        $this->categoryService->create('Category A');
        $this->categoryService->create('Category B');
        $categories = $this->categoryService->list();

        $this->assertCount(3, $categories); // Including the fixture
    }

    public function testShowCategoryById(): void
    {
        $category = $this->categoryService->create('Specific Category');
        $fetchedCategory = $this->categoryService->show($category->getId());
        $this->assertNotNull($fetchedCategory);

        $this->assertEquals('Specific Category', $fetchedCategory->getTitle());
    }
}