<?php

namespace App\Tests\Integration;

use App\DataFixtures\CategoryFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CategoryServiceIntegrationTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $databasePath = self::$kernel->getProjectDir() . '/var/test.db';
        if (file_exists($databasePath)) {
            unlink($databasePath);
        }

        $application = new Application(self::$kernel);

        $command = $application->find('doctrine:schema:drop');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--force' => true,
            '--env' => 'test'
        ]);

        $command = $application->find('doctrine:schema:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--env' => 'test'
        ]);
        // Step 2: Load fixtures using Doctrine ORM Executor
        $entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();

        $loader = new Loader();
        $loader->addFixture(new CategoryFixtures());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures());

        // Fetch the CategoryService for testing
        $this->categoryService = static::getContainer()->get('App\Service\CategoryService');

    }

    protected function tearDown(): void
    {
        // Check if kernel is booted before shutting it down
        if (self::$kernel !== null) {
            $application = new Application(self::$kernel);

            // Drop the schema to ensure a fresh database for each test
            $command = $application->find('doctrine:schema:drop');
            $commandTester = new CommandTester($command);
            $commandTester->execute([
                '--force' => true,
                '--env' => 'test',
            ]);

            // Shutdown kernel to free memory
            self::$kernel->shutdown();
            self::$kernel = null; // Explicitly set to null
        }

        parent::tearDown();
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

    public function testDeleteCategory(): void
    {
        $category = $this->categoryService->create('To Be Deleted');
        $category = $this->categoryService->delete($category->getId());
        $this->assertNull($category);

    }

    public function testFetchAllCategories(): void
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