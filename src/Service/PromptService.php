<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Prompt;
use App\Repository\PromptRepository;
use App\Service\Factory\IService;

class PromptService implements IService
{
    protected PromptRepository $promptRepository;

    public function __construct(PromptRepository $promptRepository)
    {
        $this->promptRepository = $promptRepository;
    }

    public function create(string $title, Category $category = null): Prompt
    {
        $prompt = new Prompt();
        $prompt->setTitle($title);
        if (null !== $category) {
            $prompt->setCategory($category);
        }

        return $prompt;
    }

    public function update(int $promptId, string $title = "", Category $newCategory = null): Prompt
    {
        $promptFromDb = $this->promptRepository->findBy(['promptId' => $promptId])[0];

        if ("" !== $title) {
            $promptFromDb->setTitle($title);
        }
        if (null !== $newCategory) {
            $promptFromDb->setCategory($newCategory);
        }

        $this->promptRepository->flush();

        return $this->promptRepository->findBy(['promptId' => $promptId])[0];
    }

    public function list(): array
    {
        return $this->promptRepository->findAll();
    }

    public function delete(int $id): void
    {
        $promptForDeletion = $this->promptRepository->findBy(['id' => $id])[0];
        $this->promptRepository->remove($promptForDeletion);
    }

    public function show(int $id): ?Prompt
    {
        $prompts = $this->promptRepository->findBy(['id' => $id]);
        if (empty($prompts)) {
            return null;
        }
        return $prompts[0];
    }

    public function showBy(string $criteria, $argument): ?Prompt
    {
        $prompts = $this->promptRepository->findBy([$criteria, $argument]);
        if (empty($prompts)) {
            return null;
        }
        return $prompts[0];
    }
}