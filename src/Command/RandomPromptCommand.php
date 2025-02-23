<?php

namespace App\Command;

use App\Service\PromptService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class RandomPromptCommand
 *
 * This command shows a random prompt selected from the database and allows the user to provide an answer.
 */
class RandomPromptCommand extends Command
{
    /**
     * @var PromptService The service responsible for managing prompts.
     */
    protected PromptService $promptService;

    /**
     * @var string|null The default name of the command.
     */
    protected static $defaultName = 'api:random-prompt';

    /**
     * RandomPromptCommand constructor.
     *
     * @param PromptService $promptService The service responsible for prompt operations.
     */
    public function __construct(PromptService $promptService)
    {
        $this->promptService = $promptService;
        parent::__construct();
    }

    /**
     * Configures the command options and help message.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setHelp('This command allows you to show a random prompt, selected from the database.');
    }

    /**
     * Executes the Random Prompt Command.
     *
     * Fetches a random prompt from the database, displays it, and asks the user for an answer.
     * The answer is passed to the 'api:create-note' command for processing.
     *
     * @param InputInterface $input The input interface for the console.
     * @param OutputInterface $output The output interface for the console.
     * @return int Returns Command::SUCCESS if execution was successful, Command::FAILURE otherwise.
     * @throws ExceptionInterface If the NoteCreate command fails.
     * @throws \Exception If an error occurs while fetching the random prompt.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $noteCommand = $this->getApplication()->find('api:create-note');

        try {
            $prompt = $this->promptService->showRandomPrompt();
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
        $output->writeln($prompt->getTitle());
        $output->writeln('<info>Prompt shown!</info>');

        $helper = $this->getHelper('question');
        $question = new Question('What\'s your answer?: ', '');
        $answer = $helper->ask($input, $output, $question);

        $noteCommandInput = new ArrayInput([
            'command' => 'api:create-note',
            'content' => $answer,
            'prompt' => $prompt
        ]);

        $returnCode = $noteCommand->run($noteCommandInput, $output);
        if ($returnCode !== Command::SUCCESS) {
            $output->writeln('<error>NoteCreate command failed!</error>');
        }

        return Command::SUCCESS;
    }
}