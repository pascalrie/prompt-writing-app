<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Prompt;
use App\Repository\PromptRepository;
use App\Service\Factory\IService;
use function PHPUnit\Framework\isEmpty;

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
     * @param string $title
     * @param Category|null $category
     * @return Prompt
     */
    public function create(string $title, Category $category = null): Prompt
    {
        $prompt = new Prompt();
        $prompt->setTitle($title);
        if (null !== $category) {
            $prompt->setCategory($category);
        }

        return $prompt;
    }

    /**
     * @param int $promptId
     * @param string $title
     * @param Category|null $newCategory
     * @param array $newNotes
     * @return Prompt
     */
    public function update(int $promptId, string $title = "", ?Category $newCategory = null, array $newNotes = []): Prompt
    {
        $promptFromDb = $this->promptRepository->findBy(['promptId' => $promptId])[0];

        if ("" !== $title) {
            $promptFromDb->setTitle($title);
        }

        if (null !== $newCategory) {
            $promptFromDb->setCategory($newCategory);
        }

        if ([] !== $newNotes) {
            foreach ($newNotes as $note) {
                if ($note instanceof Note) {
                    $promptFromDb->addNote($note);
                }
            }
        }

        $this->promptRepository->flush();

        return $this->promptRepository->findBy(['promptId' => $promptId])[0];
    }

    /**
     * @return array
     */
    public function list(): array
    {
        return $this->promptRepository->findAll();
    }

    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $promptForDeletion = $this->promptRepository->findBy(['id' => $id])[0];
        $this->promptRepository->remove($promptForDeletion);
    }

    /**
     * @param int $id
     * @return Prompt|null
     */
    public function show(int $id): ?Prompt
    {
        $prompts = $this->promptRepository->findBy(['id' => $id]);
        if (empty($prompts)) {
            return null;
        }
        return $prompts[0];
    }

    /**
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
}