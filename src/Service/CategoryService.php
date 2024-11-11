<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Prompt;
use App\Repository\CategoryRepository;

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
     * @param int $id
     * @param string $newTitle
     * @param array|null $newPotentialPrompts
     * @param array|null $newPotentialNotes
     * @param bool $promptsShouldBeReplaced
     * @return Category
     */
    public function update(int   $id, string $newTitle = "", array $newPotentialPrompts = [],
                           array $newPotentialNotes = [], bool $promptsShouldBeReplaced = false): Category
    {
        $oldCategoryFromDb = $this->categoryRepository->findBy(['id' => $id])[0];

        if ("" !== $newTitle) {
            $oldCategoryFromDb->setTitle($newTitle);
        }

        if ($promptsShouldBeReplaced) {
            foreach ($oldCategoryFromDb->getPrompts() as $oldPromptToDelete) {
                $oldCategoryFromDb->removePrompt($oldPromptToDelete);
                $this->categoryRepository->flush();
            }

            foreach ($newPotentialPrompts as $newPotentialPrompt) {
                $oldCategoryFromDb->addPrompt($newPotentialPrompt);
            }
        }

        if ([] !== $newPotentialPrompts && !$promptsShouldBeReplaced) {
            foreach ($newPotentialPrompts as $prompt) {
                if ($prompt instanceof Prompt) {
                    $oldCategoryFromDb->addPrompt($prompt);
                }
            }
        }

        if ([] !== $newPotentialNotes) {
            foreach ($newPotentialNotes as $note) {
                if ($note instanceof Note) {
                    $oldCategoryFromDb->addNote($note);
                }
            }
        }

        $this->categoryRepository->flush();

        return $oldCategoryFromDb;
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

        if (is_array($categories)) {
            return $categories[0];
        }
        return $categories;
    }

    public function findBy(array $array): array
    {
        return $this->categoryRepository->findBy($array);
    }
}