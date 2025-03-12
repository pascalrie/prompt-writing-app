<?php

namespace App\Tests\Integration;

use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\FolderFixtures;
use App\DataFixtures\NoteFixtures;
use App\DataFixtures\PromptFixtures;
use App\DataFixtures\TagFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

abstract class BaseIntegrationTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();

        $this->setupDatabase();
        $this->loadFixtures();
    }

    protected function tearDown(): void
    {
        $this->resetDatabase();
        $this->entityManager->clear();
        parent::tearDown();
    }

    private function setupDatabase(): void
    {
        $databasePath = self::$kernel->getProjectDir() . '/var/test.db';
        if (file_exists($databasePath)) {
            unlink($databasePath);
        }

        $application = new Application(self::$kernel);

        $command = $application->find('doctrine:schema:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--env' => 'test'
        ]);
    }

    private function resetDatabase(): void
    {
        if (self::$kernel !== null) {
            $application = new Application(self::$kernel);

            $command = $application->find('doctrine:schema:drop');
            $commandTester = new CommandTester($command);
            $commandTester->execute([
                '--force' => true,
                '--env' => 'test',
            ]);

            self::$kernel->shutdown();
            self::$kernel = null;
        }
    }

    private function loadFixtures(): void
    {
        $loader = new Loader();
        $loader->addFixture(new NoteFixtures());
        $loader->addFixture(new CategoryFixtures());
        $loader->addFixture(new PromptFixtures());
        $loader->addFixture(new FolderFixtures());
        $loader->addFixture(new TagFixtures());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }
}