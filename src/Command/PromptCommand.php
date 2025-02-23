<?php

namespace App\Command;

use App\Service\CategoryService;
use App\Service\PromptService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class PromptCommand
 *
 * This command allows the creation of prompts and associating them with categories.
 */
class PromptCommand extends Command
{
    /**
     * @var PromptService The service used for managing prompts.
     */
    protected PromptService $promptService;

    /**
     * @var CategoryService The service used for managing categories.
     */
    protected CategoryService $categoryService;

    /**
     * @var string The default name of the command.
     */
    protected static $defaultName = 'api:create-prompt';

    /**
     * PromptCommand constructor.
     *
     * @param PromptService $promptService The prompt service.
     * @param CategoryService $categoryService The category service.
     */
    public function __construct(PromptService $promptService, CategoryService $categoryService)
    {
        $this->promptService = $promptService;
        $this->categoryService = $categoryService;
        parent::__construct();
    }

    /**
     * Configures the command with a description and help message.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setHelp('This command allows you to create prompts');
    }

    /**
     * Executes the command logic for creating a prompt and assigning it to a category.
     *
     * @param InputInterface $input The input interface.
     * @param OutputInterface $output The output interface.
     * @return int The command status code (SUCCESS or FAILURE).
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        // Ask for a new prompt title
        $choice = new Question('Title of new prompt?: ');
        $answer = $helper->ask($input, $output, $choice);

        // List all categories
        $categoriesForPrompt = $this->categoryService->list();
        $categoryIds = [];
        foreach ($categoriesForPrompt as $category) {
            $output->writeln($category->getTitle() . ' - id: ' . $category->getId());
            $categoryIds[] = $category->getId();
        }

        // Ask for category ID
        $categoryChoice = new Question('Choose a category id: ');
        $answerOfCategoryChoice = $helper->ask($input, $output, $categoryChoice);

        // Validate the chosen category ID
        if (!in_array($answerOfCategoryChoice, $categoryIds)) {
            $output->writeln('Failure Category id must be a valid id.');
            return Command::FAILURE;
        }

        // Fetch the selected category and create the prompt
        $categoryChoice = $this->categoryService->show($answerOfCategoryChoice);
        $prompt = $this->promptService->create($answer, $categoryChoice);
        $output->writeln('Prompt with title: ' . $prompt->getTitle() . ' - id: ' . $prompt->getId() . ' was created.');

        return Command::SUCCESS;
    }
}