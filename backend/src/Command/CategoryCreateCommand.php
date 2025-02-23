<?php

namespace App\Command;

use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CategoryCreateCommand extends Command
{
    /**
     * Command name to be used in the console
     *
     * @var string
     */
    protected static $defaultName = 'api:create:category';

    /**
     * The EntityManager interface for database operations
     *
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    /**
     * The service layer used for managing categories
     *
     * @var CategoryService
     */
    protected CategoryService $categoryService;

    /**
     * Constructor to initialize dependencies
     *
     * @param EntityManagerInterface $entityManager The entity manager used for database interaction
     * @param CategoryService $categoryService The service to handle category logic
     */
    public function __construct(EntityManagerInterface $entityManager, CategoryService $categoryService)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->categoryService = $categoryService;
    }

    /**
     * Configures the command's description and help message
     */
    protected function configure()
    {
        $this
            ->setDescription('Creates a new category')
            ->setHelp('This command allows you to create a category and store it in the database.');
    }

    /**
     * Executes the category creation logic
     *
     * @param InputInterface $input The input interface for the command
     * @param OutputInterface $output The output interface for showing messages
     * @return int Returns the command result status: SUCCESS (0) or FAILURE (1)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $question = new Question('Enter the title of the category: ');
        $title = $helper->ask($input, $output, $question);

        if (!$title || trim($title) === '') {
            $output->writeln('<error>Invalid category title provided! Command aborted.</error>');
            return Command::FAILURE;
        }

        try {
            $category = $this->categoryService->create($title);
        } catch (\Exception $e) {
            $output->writeln('<error>Failed to create category: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>Category created successfully with name: ' . $category->getTitle() . '</info>');

        return Command::SUCCESS;
    }
}