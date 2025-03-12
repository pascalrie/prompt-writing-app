<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Prompt;
use App\Repository\CategoryRepository;
use InvalidArgumentException;

class CategoryService implements IService
{
    protected CategoryRepository $categoryRepository;

    /**
     * CategoryService constructor.
     *
     * @param CategoryRepository $categoryRepository The repository used for managing Category entities.
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Creates a new category with an optional first note and/or first prompt.
     *
     * @param string $title The title of the new category.
     * @param Note|null $firstNote An optional Note object to associate with the category.
     * @param Prompt|null $firstPrompt An optional Prompt object to associate with the category.
     * @return Category The newly created Category entity.
     */
    public function create(string $title = "", ?Note $firstNote = null, ?Prompt $firstPrompt = null): Category
    {
        $category = new Category();
        $category->setTitle($title);

        $existingCategory = $this->categoryRepository->findOneBy(['title' => $title]);
        if ($existingCategory) {
            throw new InvalidArgumentException('A category with this title already exists.');
        }

        if ($firstNote) {
            $category->addNote($firstNote);
        }

        if ($firstPrompt) {
            $category->addPrompt($firstPrompt);
        }

        return $this->categoryRepository->add($category, true);
    }

    /**
     * Updates an existing category by its ID and modifies its properties.
     *
     * @param int $id The ID of the Category to update.
     * @param string|null $newTitle The new title for the category (if provided).
     * @param Note[] $newNotes An array of Note objects to add to the category.
     * @param Prompt[] $newPrompts An array of Prompt objects to add to the category.
     * @return Category The updated Category entity.
     * @throws InvalidArgumentException If the Category with the given ID is not found.
     */
    public function update(int $id, string $newTitle = null, array $newNotes = [], array $newPrompts = []): Category
    {
        $category = $this->findCategoryById($id);

        if ($category === null) {
            throw new InvalidArgumentException("Category with ID {$id} not found.");
        }

        if (!empty($newTitle)) {
            $category->setTitle($newTitle);
        }

        foreach ($newNotes as $note) {
            if (!$note instanceof Note) {
                throw new InvalidArgumentException('Invalid Note object provided in "newNotes" array.');
            }
            $category->addNote($note);
        }

        foreach ($newPrompts as $prompt) {
            if (!$prompt instanceof Prompt) {
                throw new InvalidArgumentException('Invalid Prompt provided in "newPrompts" array.');
            }
            $prompt->setCategory($category);
            $category->addPrompt($prompt);
        }

        $this->categoryRepository->flush();

        return $category;
    }

    /**
     * Retrieves a list of all available categories.
     *
     * @return Category[]|array An array of all Category entities.
     */
    public function list(): array
    {
        return $this->categoryRepository->findAll();
    }

    /**
     * Deletes a category by its ID.
     *
     * @param int $id The ID of the Category to delete.
     * @return void
     * @throws InvalidArgumentException If the Category with the given ID is not found.
     */
    public function delete(int $id): void
    {
        $category = $this->findCategoryById($id);

        if ($category === null) {
            throw new InvalidArgumentException("Category with ID {$id} not found.");
        }

        $this->categoryRepository->remove($category, true);
    }

    /**
     * Shows a category by its ID.
     *
     * @param int $id The ID of the Category to retrieve.
     * @return Category|null The Category entity if found, or null otherwise.
     */
    public function show(int $id): ?Category
    {
        return $this->findCategoryById($id) ?: null;
    }

    /**
     * Shows a category by its title.
     *
     * @param string $title The title of the Category to retrieve.
     * @return Category|null The Category entity if found, or null otherwise.
     */
    public function showByTitle(string $title): ?Category
    {
        return $this->categoryRepository->findOneBy(['title' => $title]) ?? null;
    }

    /**
     * Helper method to find a category by its ID.
     *
     * @param int $id The ID of the Category to search for.
     * @return Category|null The Category entity if found, or null otherwise.
     */
    private function findCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->findOneBy(['id' => $id]) ?? null;
    }
}