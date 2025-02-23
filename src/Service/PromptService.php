<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Prompt;
use App\Repository\PromptRepository;
use Exception;

class PromptService implements IService
{
    protected PromptRepository $promptRepository;

    /**
     * @param PromptRepository $promptRepository
     */
    public function __construct(PromptRepository $promptRepository)
    {
        $this->promptRepository = $promptRepository;
    }

    /**
     * Creates a new prompt with an optional category
     * @param string $title
     * @param Category|null $category
     * @return Prompt
     */
    public function create(string $title, ?Category $category = null): Prompt
    {
        $prompt = new Prompt();
        $prompt->setTitle($title);

        if ($category !== null) {
            $prompt->setCategory($category);
        }

        $this->promptRepository->add($prompt, true);
        return $prompt;
    }

    /**
     * Updates a prompt's details (title, category, and notes).
     *
     * @param int $promptId
     * @param string|null $title
     * @param Category|null $newCategory
     * @param array $newNotes
     * @return Prompt
     */
    public function update(int $promptId, ?string $title = "", ?Category $newCategory = null, array $newNotes = []): Prompt
    {
        $prompt = $this->findPromptById($promptId);

        if ($prompt === null) {
            throw new \InvalidArgumentException("Prompt with ID {$promptId} not found.");
        }

        if (!empty($title)) {
            $prompt->setTitle($title);
        }

        if ($newCategory !== null) {
            $prompt->setCategory($newCategory);
        }

        foreach ($newNotes as $note) {
            if ($note instanceof Note) {
                $prompt->addNote($note);
            }
        }

        $this->promptRepository->flush();

        return $prompt;
    }

    /**
     * Returns a list of all prompts.
     *
     * @return Prompt[]
     */
    public function list(): array
    {
        return $this->promptRepository->findAll();
    }

    /**
     * Deletes a prompt by its ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $promptForDeletion = $this->findPromptById($id);
        if ($promptForDeletion === null) {
            throw new \InvalidArgumentException("Prompt with ID {$id} not found.");
        }

        $this->promptRepository->remove($promptForDeletion);
    }

    /**
     * @param int $id
     * @return Prompt|null
     */
    public function show(int $id): ?Prompt
    {
        return $this->findPromptById($id);
    }

    /**
     *  Finds a prompt using a specific criteria.
     * @param string $criteria
     * @param $argument
     * @return Prompt|null
     */
    public function showBy(string $criteria, $argument): ?Prompt
    {
        $prompts = $this->promptRepository->findBy([$criteria, $argument]);
        if (empty($prompts)) {
            return null;
        }
        return $prompts[0];
    }

    /**
     * Returns a random prompt from the repository.
     * @throws Exception
     */
    public function showRandomPrompt(): Prompt
    {
        $prompts = $this->promptRepository->findAll();

        if (empty($prompts)) {
            throw new Exception("Please create a prompt first.");
        }

        return $prompts[array_rand($prompts)];

    }

    private function findPromptById(int $id): ?Prompt
    {
        return $this->promptRepository->findBy(['id' => $id])[0] ?? null;
    }
}