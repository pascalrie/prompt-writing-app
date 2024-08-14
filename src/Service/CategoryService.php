<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Prompt;
use App\Repository\CategoryRepository;

class CategoryService
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function create(string $title, Note $firstNote = null, Prompt $firstPrompt = null): Category
    {
        $category = new Category();
        $category->setTitle($title);
        if ($firstNote !== null) {
            $category->addNote($firstNote);
        }

        if ($firstPrompt !== null) {
            $category->addPrompt($firstPrompt);
        }

        $this->categoryRepository->add($category);
        return $category;
    }

    public function update(int $oldCategoryId, string $newTitle = "", array $potentialNewPrompts = null,
                           array $potentialNewNotes = null): void
    {
        $categoryInDb = $this->categoryRepository->findBy(['id' => $oldCategoryId])[0];

        if (null !== $newTitle) {
            $categoryInDb->setTitle($newTitle);
        }

        if (null !== $potentialNewPrompts) {
            $this->addPromptsIfNotInCategory($potentialNewPrompts, $categoryInDb);
        }

        if (null !== $potentialNewNotes) {
            $this->addNotesIfNotInCategory($potentialNewNotes, $categoryInDb);
        }

        $this->categoryRepository->flush();
    }

    public function list(): array
    {
        return $this->categoryRepository->findAll();
    }

    public function delete(int $id): void
    {
        $category = $this->categoryRepository->findBy(['id' => $id])[0];
        $this->categoryRepository->remove($category);

    }

    public function show(int $id): Category
    {
        return $this->categoryRepository->findBy(['id' => $id])[0];
    }

    private function isPotentialNewPromptAlreadyInCategory(int $categoryId, int $promptId): bool
    {
        $promptsForGivenCategory = $this->categoryRepository->findBy(['id' => $categoryId])[0]->getPrompts();
        foreach ($promptsForGivenCategory as $promptInCategory) {
            if ($promptInCategory->getId() === $promptId) {
                return true;
            }
        }
        return false;
    }

    private function isPotentialNewNoteAlreadyInCategory(int $categoryId, int $noteId): bool
    {
        $notesForGivenCategory = $this->categoryRepository->findBy(['id' => $categoryId])[0]->getNotes();
        foreach ($notesForGivenCategory as $noteAlreadyInCategory) {
            if ($noteAlreadyInCategory->getId() === $noteId) {
                return true;
            }
        }
        return false;
    }

    private function addPromptsIfNotInCategory (array $promptsToAdd, Category $category): void
    {
        foreach ($promptsToAdd as $promptToAdd) {
            $isAssociated = $this->isPotentialNewPromptAlreadyInCategory($category->getId(), $promptToAdd->getId());
            if (!$isAssociated) {
                $category->addPrompt($promptToAdd);
            }
        }
    }

    private function addNotesIfNotInCategory (array $notesToAdd, Category $category): void
    {
        foreach ($notesToAdd as $noteToAdd) {
            $isAssociated = $this->isPotentialNewNoteAlreadyInCategory($category->getId(), $noteToAdd->getId());
            if (!$isAssociated) {
                $category->addNote($noteToAdd);
            }
        }
    }
}