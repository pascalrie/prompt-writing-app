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

        $promptService = new PromptService($this->repoMock);
        $newCategory = new Category();
        $newCategory->setTitle('updated example category');
        $newCategory->setId(2);

        $updatedPrompt = $promptService->update($this->examplePrompt->getId(), "", $newCategory);

        $this->assertNotNull($updatedPrompt);
        $this->assertEquals($newCategory, $updatedPrompt->getCategory());
        $this->assertEquals($newCategory->getId(), $updatedPrompt->getCategory()->getId());
    }

    public function testUpdateAddOnlyNoteOfPrompt(): void
    {
        $this->repoMock->method('findBy')->willReturn([$this->examplePrompt]);

        $promptService = new PromptService($this->repoMock);
        $newNoteToAdd = new Note();
        $newNoteToAdd->setId(2);
        $newNoteToAdd->setTitle('new note title');

        $updatedPrompt = $promptService->update($this->examplePrompt->getId(), "", null, [$newNoteToAdd]);
        $this->assertNotNull($updatedPrompt);
        $this->assertEquals($newNoteToAdd, $updatedPrompt->getNotes()[0]);
    }

    public function testShowOfPrompt(): void
    {
        $this->repoMock->method('findBy')->willReturn([$this->examplePrompt]);
        $promptService = new PromptService($this->repoMock);
        $shownPrompt = $promptService->show($this->examplePrompt->getId());
        $this->assertNotNull($shownPrompt);
        $this->assertEquals($this->examplePrompt, $shownPrompt);
    }

    public function testShowOfNonExistingPrompt(): void
    {
        $this->repoMock->method('findBy')->willReturn(null);
        $promptService = new PromptService($this->repoMock);
        // prompt should be null, because id is way too high
        $shownPromptShouldBeNull = $promptService->show(999);
        $this->assertNull($shownPromptShouldBeNull);
    }

    public function testShowPromptByCriteria(): void
    {
        $this->repoMock->method('findBy')->willReturn([$this->examplePrompt]);
        $promptService = new PromptService($this->repoMock);

        $prompt = $promptService->showBy('id', $this->examplePrompt->getId());
        $this->assertNotNull($prompt);
        $this->assertEquals($this->examplePrompt, $prompt);
    }

    public function testShowByNonExistingPrompt(): void
    {
        $this->repoMock->method('findBy')->willReturn(null);
        $promptService = new PromptService($this->repoMock);
        // prompt should be null, because id is way too high
        $shownPromptShouldBeNull = $promptService->showBy('id', 999);
        $this->assertNull($shownPromptShouldBeNull);
    }
}