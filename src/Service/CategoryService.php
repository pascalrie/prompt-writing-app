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
     * Creates a new category with optional first note and first prompt.
     *
     * @param string $title
     * @param Note|null $firstNote
     * @param Prompt|null $firstPrompt
     * @return Category
     */
    public function create(string $title = "", ?Note $firstNote = null, ?Prompt $firstPrompt = null): Category
    {
        $category = new Category();
        $category->setTitle($title);

        if ($firstNote) {
            $category->addNote($firstNote);
        }

        if ($firstPrompt) {
            $category->addPrompt($firstPrompt);
        }

        return $this->categoryRepository->add($category, true);
    }

    /**
     * @param int $id
     * @param string|null $newTitle
     * @param Prompt[] $newPrompts
     * @param Note[] $newNotes
     * @param bool $replacePrompts
     * @return Category
     */
    public function update(int   $id, string $newTitle = null, array $newPrompts = [],
                           array $newNotes = [], bool $replacePrompts = false): Category
    {
        $category = $this->findCategoryById($id);

        if ($category === null) {
            throw new \InvalidArgumentException("Category with ID {$id} not found.");
        }

        if (!empty($newTitle)) {
            $category->setTitle($newTitle);
        }

        if ($replacePrompts) {
           $category->clearPrompts();
        }

        foreach ($newPrompts as $prompt) {
            if ($prompt instanceof Prompt) {
                $category->addPrompt($prompt);
            }
        }

        foreach ($newNotes as $note) {
            if ($note instanceof Note) {
                $category->addNote($note);
            }
        }

        $this->categoryRepository->flush();

        return $category;
    }

    /**
     * Returns a list of all categories.
     *
     * @return array
     */
    public function list(): array
    {
        return $this->categoryRepository->findAll();
    }

    /**
     * Deletes a category by its ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $category = $this->findCategoryById($id);

        if ($category === null) {
            throw new \InvalidArgumentException("Category with ID {$id} not found.");
        }

        $this->categoryRepository->remove($category, true);
    }

    /**
     * Finds a category by its ID.
     *
     * @param int $id
     * @return Category|null
     */
    public function show(int $id): ?Category
    {
        return $this->findCategoryById($id) ?: null;
    }

    /**
     * Finds a category by its title.
     *
     * @param string $title
     * @return Category|null
     */
    public function showByTitle(string $title): ?Category
    {
        return $this->categoryRepository->findOneBy(['title' => $title]);
    }

    private function findCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->findBy(['id' => $id])[0] ?? null;
    }
}