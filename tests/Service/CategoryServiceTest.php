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
        $this->exampleCategory->setTitle($title);
        $this->exampleCategory->addNote($note);
        $this->exampleCategory = $this->exampleCategory->addPrompt($prompt);

        $this->repoMock = $this->getMockBuilder(CategoryRepository::class)
            ->setConstructorArgs([$this->managerRegistry])
            ->onlyMethods(['add', 'persist', 'flush'])
            ->onlyMethods(['findBy'])
            ->getMock();
    }

    public function tearDown(): void
    {
        $this->exampleCategory = null;
        $this->repoMock = null;
    }

    public function testCreateCategoryWithSetUpMethod(): void
    {
        $this->setUp();

        $this->repoMock->method('findBy')->willReturn($this->exampleCategory);
        $this->repoMock->method('add')->willReturn($this->exampleCategory);

        $categoryService = new CategoryService($this->repoMock);
        $categoryService->create($this->exampleCategory->getTitle(), $this->exampleCategory->getNotes()[0],
                $this->exampleCategory->getPrompts()[0]);
        $category = $categoryService->showByTitle($this->exampleCategory->getTitle());

        $this->assertNotNull($category);
        $this->assertEquals($this->exampleCategory->getTitle(), $category->getTitle());
        $this->assertEquals($this->exampleCategory->getPrompts()[0], $category->getPrompts()[0]);
        $this->assertEquals($this->exampleCategory->getNotes()[0], $category->getNotes()[0]);
    }

    public function testUpdateCategory(): void
    {
        $this->setUp();

    }
}