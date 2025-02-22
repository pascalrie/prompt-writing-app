<?php

namespace App\Command;

use App\Service\CategoryService;
use App\Service\PromptService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class PromptCommand extends Command
{
    protected PromptService $promptService;

    protected CategoryService $categoryService;

    protected static $defaultName = 'api:create-prompt';

    public function __construct(PromptService $promptService, CategoryService $categoryService)
    {
        $this->promptService = $promptService;
        $this->categoryService = $categoryService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setHelp('This command allows you to create prompts');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $choice = new Question('Title of new prompt?: ');
        $answer = $helper->ask($input, $output, $choice);
        $categoriesForPrompt = $this->categoryService->list();
        $categoryIds = [];
        foreach ($categoriesForPrompt as $category) {
            $output->writeln($category->getTitle() . ' - id: ' . $category->getId());
            $categoryIds[] = $category->getId();
        }

        $categoryChoice = new Question('Choose a category id: ');
        $answerOfCategoryChoice = $helper->ask($input, $output, $categoryChoice);

        if (!in_array($answerOfCategoryChoice, $categoryIds)) {
            $output->writeln('Failure Category id must be a valid id.');
            return Command::FAILURE;
        }
        $categoryChoice = $this->categoryService->show($answerOfCategoryChoice);
        $prompt = $this->promptService->create($answer, $categoryChoice);
        $output->writeln('Prompt with title: ' . $prompt->getTitle() . ' - id: ' . $prompt->getId() . ' was created.');
        return Command::SUCCESS;
    }
}