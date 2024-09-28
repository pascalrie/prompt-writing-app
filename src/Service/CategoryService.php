<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Prompt;
use App\Repository\CategoryRepository;
use App\Service\Factory\IService;

class CategoryService implements IService
{
    protected CategoryRepository $categoryRepository;

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param string $title
     * @param Note|null $firstNote
     * @param Prompt|null $firstPrompt
     * @return Category
     */
    public function create(string $title = "", Note $firstNote = null, Prompt $firstPrompt = null): Category
    {
        $category = new Category();

        if (null !== $title) {
            $category->setTitle($title);
        }

        if (null !== $firstNote) {
            $category->addNote($firstNote);
        }

        if (null !== $firstPrompt) {
            $category->addPrompt($firstPrompt);
        }

        return $this->categoryRepository->add($category, true);
    }

    /**
     * @param int $oldCategoryId
     * @param string $newTitle
     * @param array|null $newPotentialPrompts
     * @param array|null $newPotentialNotes
     * @return Category
     */
    public function update(int   $oldCategoryId, string $newTitle = "", array $newPotentialPrompts = null,
                           array $newPotentialNotes = null): Category
    {
        $categoryInDb = $this->categoryRepository->findBy(['id' => $oldCategoryId])[0];

        if (null !== $newTitle) {
            $categoryInDb->setTitle($newTitle);
        }

        if (null !== $newPotentialPrompts) {
            foreach ($newPotentialPrompts as $prompt) {
                $categoryInDb->addPrompt($prompt);
            }
        }

        if (null !== $newPotentialNotes) {
            foreach ($newPotentialNotes as $note) {
                $categoryInDb->addNote($note);
            }
        }

        $this->categoryRepository->flush();

        return $this->show($oldCategoryId);
    }

    /**
     * @return array
     */
    public function list(): array
    {
        return $this->categoryRepository->findAll();
    }

    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $category = $this->categoryRepository->findBy(['id' => $id])[0];

        $this->categoryRepository->remove($category, true);
    }

    /**
     * @param int $id
     * @return Category|null
     */
    public function show(int $id): ?Category
    {
        $categories = $this->categoryRepository->findBy(['id' => $id]);
        if (empty($categories)) {
            return null;
        }
        return $categories[0];
    }

    /**
     * @param string $title
     * @return Category|null
     */
    public function showByTitle(string $title): ?Category
    {
        $categories = $this->categoryRepository->findBy(['title' => $title]);
        if (empty($categories)) {
            return null;
        }
        return $categories[0];
    }
}