<?php

namespace App\Command;

use App\Entity\Category;
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
    protected NoteService  $noteService;

    protected CategoryService $categoryService;

    protected TagService $tagService;

    protected static $defaultName = 'api:create-note';

    public function __construct(NoteService $noteService, CategoryService $categoryService, TagService $tagService)
    {
        $this->noteService = $noteService;
        $this->categoryService = $categoryService;
        $this->tagService = $tagService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setHelp('This command allows you to create a note');
        $this->addArgument('content', InputArgument::REQUIRED, 'The content of the Note.');
        $this->addArgument('prompt', InputArgument::REQUIRED, 'The prompt of the Note.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tag = $this->tagService->showOneBy('title', '#toSort');
        if (null === $tag) {
            $tag = $this->tagService->create('#toSort');
        }

        $tags = new ArrayCollection();
        $tags->add($tag);

        $category = $this->categoryService->showByTitle('Console');
        if (null === $category) {
            $category = $this->categoryService->create('Console');
        }

        $this->noteService->create((new \DateTime('NOW'))->format('d-m-Y H:i'), $input->getArgument('content'), $tags, $category, $input->getArgument('prompt'));
        $output->writeln('<info>Note generated!</info>');
        return Command::SUCCESS;
    }

}