<?php

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Prompt;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CategoryServiceTest extends TestCase
{
    protected ManagerRegistry $managerRegistry;

    protected ?Category $exampleCategory;

    protected ?MockObject $repoMock;

    protected ?CategoryService $categoryService;

    public function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $title = 'Category title 1';

        $this->exampleCategory = new Category();

        $this->exampleCategory->setId(1);
        $this->exampleCategory->setTitle($title);

        $this->repoMock = $this->getMockBuilder(CategoryRepository::class)
            ->setConstructorArgs([$this->managerRegistry])
            ->onlyMethods(['add', 'persist', 'flush', 'findBy', 'findAll'])
            ->getMock();

        $this->categoryService = new CategoryService($this->repoMock);
    }

    public function tearDown(): void
    {
        $this->exampleCategory = null;
        $this->repoMock = null;
        $this->categoryService = null;
    }

    public function testCategoryUpdateTitle(): void
    {
        $this->repoMock->method('findBy')->willReturn([$this->exampleCategory]);
        $newTitle = 'new category title';
        $updatedCategory = $this->categoryService->update($this->exampleCategory->getId(), $newTitle);

        $this->assertNotNull($updatedCategory);
        $this->assertEquals($newTitle, $updatedCategory->getTitle());
    }

    public function testCategoryUpdatePrompts(): void
    {
        $this->repoMock->method('findBy')->willReturn([$this->exampleCategory]);
        $newPromptToAdd = new Prompt();
        $newPromptToAdd->setTitle('new prompt');
        $newPromptToAdd->setId(1);

        $updatedCategory = $this->categoryService->update($this->exampleCategory->getId(), "", [$newPromptToAdd]);
        $this->assertNotNull($updatedCategory);
        $this->assertEquals($newPromptToAdd, $updatedCategory->getPrompts()[0]);
    }

    public function testCategoryUpdateNotes(): void
    {
        $this->repoMock->method('findBy')->willReturn([$this->exampleCategory]);
        $newNote = new Note();
        $newNote->setTitle('new note');
        $newNote->setId(1);
        $newNote->setContent('content of new note');

        $updatedCategory = $this->categoryService->update($this->exampleCategory->getId(), "", [], [$newNote]);
        $this->assertNotNull($updatedCategory);
        $this->assertEquals($newNote, $updatedCategory->getNotes()[0]);
    }

    public function testCategoryShowShouldBeNonExisting(): void
    {
        $this->repoMock->method('findBy')->willReturn([]);
        $this->exampleCategory->setId(1000);

        $shownCategoryShouldBeNull = $this->categoryService->show($this->exampleCategory->getId());

        $this->assertNull($shownCategoryShouldBeNull);
    }

    public function testCategoryShow(): void
    {
        $this->repoMock->method('findBy')->willReturn([$this->exampleCategory]);
        $shownCategory = $this->categoryService->show($this->exampleCategory->getId());

        $this->assertNotNull($shownCategory);
        $this->assertEquals($this->exampleCategory, $shownCategory);
    }

    public function testCategoryShowByTitleShouldBeNonExisting(): void
    {
        $this->repoMock->method('findBy')->willReturn([]);
        $title = 'impossible category';

        $shownCategory = $this->categoryService->showByTitle($title);

        $this->assertNull($shownCategory);
    }

    public function testCategoryShowByTitleShouldExist(): void
    {
        $this->repoMock->method('findBy')->willReturn([$this->exampleCategory]);
        $shownCategory = $this->categoryService->showByTitle($this->exampleCategory->getTitle());

        $this->assertNotNull($shownCategory);
        $this->assertEquals($this->exampleCategory, $shownCategory);
    }

    public function testCategoryList(): void
    {
        $secondCategory = new Category();
        $secondCategory->setTitle('second category');
        $secondCategory->setId(2);

        $this->repoMock->method('findAll')->willReturn([$this->exampleCategory, $secondCategory]);
        $listedCategories = $this->categoryService->list();

        $this->assertNotNull($listedCategories);
        $this->assertCount(2, $listedCategories);
    }
}