<?php

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Prompt;
use App\Repository\CategoryRepository;
use App\Repository\PromptRepository;
use App\Service\CategoryService;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CategoryServiceTest extends TestCase
{
    protected ManagerRegistry $managerRegistry;

    protected ?Category $exampleCategory;

    protected ?MockObject $repoMock;

    public function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $title = 'Category 1';

        $note = new Note();
        $note->setTitle('note 1');
        $note->setContent('Hallo Welt');

        $prompt = new Prompt();
        $prompt->setTitle('Prompt 1');

        $this->exampleCategory = new Category();

        $this->exampleCategory->setId(1);
        $this->exampleCategory->setTitle($title);
        $this->exampleCategory->addNote($note);
        $this->exampleCategory = $this->exampleCategory->addPrompt($prompt);

        $this->repoMock = $this->getMockBuilder(CategoryRepository::class)
            ->setConstructorArgs([$this->managerRegistry])
            ->onlyMethods(['add', 'persist', 'flush', 'findBy', 'findAll'])
            ->getMock();
    }

    public function tearDown(): void
    {
        $this->exampleCategory->setId(null);
        $this->exampleCategory = null;
        $this->repoMock = null;
    }

    public function testUpdateCategory(): void
    {
        $this->repoMock
            ->method('findBy')->willReturn([$this->exampleCategory]);

        $categoryService = new CategoryService($this->repoMock);

        $newTitle = 'New Title';
        $firstPrompt = new Prompt();
        $firstPrompt->setTitle($newTitle . ' prompt 1');
        $secondPrompt = new Prompt();
        $secondPrompt->setTitle($newTitle . ' prompt 2');
        $firstNote = new Note();
        $firstNote->setTitle($newTitle . ' note 1');
        $firstNote->setContent('Hallo Welt 1');
        $secondNote = new Note();
        $secondNote->setTitle($newTitle . ' note 2');
        $secondNote->setContent('Hallo Welt 2');
        $newPrompts = [$firstPrompt, $secondPrompt];
        $newNotes = [$firstNote, $secondNote];

        $updatedCategory = $categoryService->update($this->exampleCategory->getId(), $newTitle, $newPrompts, $newNotes);

        $this->assertNotNull($updatedCategory);
        $this->assertEquals($newTitle, $updatedCategory->getTitle());

        // IMPORTANT: expected index = expected index + 1, because they are only added, not replaced!
        $this->assertEquals($firstPrompt, $updatedCategory->getPrompts()[1]);
        $this->assertEquals($firstNote, $updatedCategory->getNotes()[1]);
        $this->assertEquals($secondPrompt, $updatedCategory->getPrompts()[2]);
        $this->assertEquals($secondNote, $updatedCategory->getNotes()[2]);
    }

    // test of deletion not useful, because only the mocking would be tested (on service-level (unit-test-level))

    public function testShowCategory(): void
    {
        $this->repoMock->method('findBy')->willReturn([$this->exampleCategory]);
        $categoryService = new CategoryService($this->repoMock);
        $category = $categoryService->show($this->exampleCategory->getId());
        $this->assertNotNull($category);
    }

    public function testShowCategoriesByTitle(): void
    {
        $this->repoMock->method('findBy')->willReturn([$this->exampleCategory]);
        $categoryService = new CategoryService($this->repoMock);
        $category = $categoryService->showByTitle($this->exampleCategory->getTitle());
        $this->assertNotNull($category);
    }

    public function testListCategories(): void
    {
        $secondCategory = new Category();
        $secondCategory->setId(2);
        $title = 'second category';
        $secondCategory->setTitle($title);

        $this->repoMock->method('findAll')->willReturn([$this->exampleCategory, $secondCategory]);
        $categoryService = new CategoryService($this->repoMock);

        $categories = $categoryService->list();

        $this->assertCount(2, $categories);
        $this->assertNotNull($categories[0]);
        $this->assertNotNull($categories[1]);
        $this->assertEquals('Category 1', $categories[0]->getTitle());
        $this->assertEquals($title, $categories[1]->getTitle());
    }
}