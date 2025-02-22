<?php

namespace App\Tests\Integration;

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
}