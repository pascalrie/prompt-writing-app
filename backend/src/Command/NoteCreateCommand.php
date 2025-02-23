<?php

namespace App\Command;

use App\Service\CategoryService;
use App\Service\NoteService;
use App\Service\TagService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NoteCreateCommand extends Command
{
    /**
     * @var NoteService The service for managing notes.
     */
    protected NoteService $noteService;

    /**
     * @var CategoryService The service for managing categories.
     */
    protected CategoryService $categoryService;

    /**
     * @var TagService The service for managing tags.
     */
    protected TagService $tagService;

    /**
     * @var string The default command name.
     */
    protected static $defaultName = 'api:create:note';

    /**
     * NoteCreateCommand constructor.
     *
     * @param NoteService $noteService The note service instance.
     * @param CategoryService $categoryService The category service instance.
     * @param TagService $tagService The tag service instance.
     */
    public function __construct(NoteService $noteService, CategoryService $categoryService, TagService $tagService)
    {
        $this->noteService = $noteService;
        $this->categoryService = $categoryService;
        $this->tagService = $tagService;

        parent::__construct();
    }

    /**
     * Configures the command options and arguments.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to create a note');
        $this->addArgument('content', InputArgument::REQUIRED, 'The content of the Note.');
        $this->addArgument('prompt', InputArgument::REQUIRED, 'The prompt of the Note.');
    }

    /**
     * Executes the command to create a note.
     *
     * @param InputInterface $input The input interface.
     * @param OutputInterface $output The output interface.
     *
     * @return int Returns the command success status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tag = $this->tagService->showOneBy('title', '#toSort');
        $tagConsole = $this->tagService->showOneBy('title', '#console');

        if (null === $tag) {
            $tag = $this->tagService->create('#toSort');
        }

        if ($tagConsole === null) {
            $tagConsole = $this->tagService->create('#console');
        }

        $tags = new ArrayCollection();

        $tags->add($tag);
        $tags->add($tagConsole);

        $category = $this->categoryService->showByTitle('Console');
        if (null === $category) {
            $category = $this->categoryService->create('Console');
        }

        $this->noteService->create((new \DateTime('NOW'))->format('d-m-Y H:i'), $input->getArgument('content'), $tags, $category, $input->getArgument('prompt'));
        $output->writeln('<info>Note generated!</info>');
        return Command::SUCCESS;
    }
}