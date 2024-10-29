<?php

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Prompt;
use App\Repository\PromptRepository;
use App\Service\PromptService;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PromptServiceTest extends TestCase
{
    protected ManagerRegistry $managerRegistry;

    protected ?Prompt $examplePrompt;

    protected ?MockObject $repoMock;

    public function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);

        $title = 'Example prompt title?';

        $this->examplePrompt = new Prompt();
        $this->examplePrompt->setId(1);
        $this->examplePrompt->setTitle($title);

        $category = new Category();
        $category->setTitle('example category');
        $category->setId(1);

        $this->examplePrompt->setCategory($category);

        $firstNote = new Note();
        $firstNote->setTitle('example first note');
        $firstNote->setId(1);
        $firstNote->setCategory($category);
        $secondNote = new Note();
        $secondNote->setId(2);
        $secondNote->setTitle('example second note');
        $secondNote->setCategory($category);

        $this->repoMock = $this->getMockBuilder(PromptRepository::class)
            ->setConstructorArgs([$this->managerRegistry])
            ->onlyMethods(['add', 'persist', 'flush', 'findBy', 'findAll'])
            ->getMock();
    }

    public function tearDown(): void
    {
        $this->examplePrompt->setId(null);
        $this->examplePrompt = null;
        $this->repoMock = null;
    }

    public function testUpdateOnlyTitleOfPrompt(): void
    {
        $this->repoMock->method('findBy')->willReturn([$this->examplePrompt]);

        $promptService = new PromptService($this->repoMock);

        $newTitle = 'New Title Prompt';
        $updatedPrompt = $promptService->update($this->examplePrompt->getId(), $newTitle);

        $this->assertNotNull($updatedPrompt);
        $this->assertEquals($newTitle, $updatedPrompt->getTitle());
    }

    public function testUpdateOnlyCategoryOfPrompt(): void
    {
        $this->repoMock->method('findBy')->willReturn([$this->examplePrompt]);
    }
}