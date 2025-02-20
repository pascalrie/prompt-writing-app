<?php

namespace App\Command;

use App\Service\PromptService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class RandomPromptCommand extends Command
{
    protected PromptService $promptService;

    protected static $defaultName = 'api:random-prompt';

    public function __construct(PromptService $promptService)
    {
        $this->promptService = $promptService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setHelp('This command allows you to show a random prompt, selected from the database.');
    }

    /**
     * @throws ExceptionInterface
     * @throws \Exception
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