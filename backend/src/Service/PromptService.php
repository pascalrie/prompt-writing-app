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
     * Constructor for the PromptService.
     *
     * @param PromptRepository $promptRepository The repository for managing Prompt entities.
     */
    public function __construct(PromptRepository $promptRepository)
    {
        $this->promptRepository = $promptRepository;
    }

    /**
     * Creates a new Prompt entity with an optional associated Category.
     *
     * @param string $title The title of the prompt.
     * @param Category|null $category An optional Category to associate with the prompt.
     * @return Prompt The created Prompt entity.
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
     * Updates an existing Prompt entity's attributes including title, category, and notes.
     *
     * @param int $promptId The ID of the prompt to update.
     * @param string|null $title The new title for the prompt (optional).
     * @param Category|null $newCategory The new Category to associate with the prompt (optional).
     * @param array $newNotes An array of Note entities to associate with the prompt.
     * @return Prompt The updated Prompt entity.
     * @throws \InvalidArgumentException If the specified prompt does not exist.
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
     * Retrieves a list of all Prompt entities in the repository.
     *
     * @return Prompt[] An array of all Prompt entities.
     */
    public function list(): array
    {
        return $this->promptRepository->findAll();
    }

    /**
     * Deletes a Prompt entity based on its ID.
     *
     * @param int $id The ID of the prompt to delete.
     * @return void
     * @throws \InvalidArgumentException If the specified prompt does not exist.
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
     * Retrieves a Prompt entity by its ID.
     *
     * @param int $id The ID of the prompt to retrieve.
     * @return Prompt|null The Prompt entity, or null if not found.
     */
    public function show(int $id): ?Prompt
    {
        return $this->findPromptById($id);
    }

    /**
     * Finds a Prompt entity based on a specific criteria.
     *
     * @param string $criteria The field to search by (e.g., 'title', 'id').
     * @param mixed $argument The value to search for.
     * @return Prompt|null The first matching Prompt entity, or null if none found.
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
     * Retrieves a random Prompt entity from the repository.
     *
     * @return Prompt A randomly selected Prompt entity.
     * @throws Exception If there are no prompts available.
     */
    public function showRandomPrompt(): Prompt
    {
        $prompts = $this->promptRepository->findAll();

        if (empty($prompts)) {
            throw new Exception("Please create a prompt first.");
        }

        return $prompts[array_rand($prompts)];
    }

    /**
     * Finds a Prompt entity by its ID.
     *
     * @param int $id The ID of the prompt to retrieve.
     * @return Prompt|null The Prompt entity if found, or null if not found.
     */
    private function findPromptById(int $id): ?Prompt
    {
        return $this->promptRepository->findBy(['id' => $id])[0] ?? null;
    }
}