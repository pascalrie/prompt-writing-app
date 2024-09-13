<?php

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Prompt;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class CategoryServiceTest extends TestCase
{
    protected ManagerRegistry $managerRegistry;

    public function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
    }

    public function testCreateCategory()
    {
        $this->setUp();
        $title = 'Category 1';

        $note = new Note();
        $note->setTitle('note 1');
        $note->setContent('Hallo Welt');

        $prompt = new Prompt();
        $prompt->setTitle('prompt 1');

        $createdCategory = new Category();
        $createdCategory->setTitle($title);
        $createdCategory->addNote($note);
        $createdCategory->addPrompt($prompt);
        // TODO: SQLite TestDB
        $repoMock = $this->getMockBuilder(CategoryRepository::class)
            ->setConstructorArgs([$this->managerRegistry])
            ->onlyMethods(['add', 'persist', 'flush'])
            ->onlyMethods(['findBy'])
            ->getMock();

        $repoMock->method('add')->willReturn($createdCategory);

        $categoryService = new CategoryService($repoMock);
        $category = $categoryService->create($title, $note, $prompt);
        $category = $categoryService->show($category->getId());

        $this->assertNotNull($category);
        $this->assertEquals($title, $category->getTitle());
        $this->assertEquals($prompt->getTitle(), $category->getPrompts()[0]->getTitle());
        $this->assertEquals($createdCategory->getNotes()[0]->getTitle(), $category->getNotes()[0]->getTitle());
        $this->assertEquals($createdCategory->getNotes()[0]->getContent(), $category->getNotes()[0]->getContent());
    }
}