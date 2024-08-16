<?php

namespace App\Controller\Api;

use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use App\Service\NoteService;
use App\Service\PromptService;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryApiController extends BaseApiController
{
    protected CategoryService $categoryService;

    protected PromptService $promptService;

    protected NoteService $noteService;

    public function __construct(CategoryService $categoryService, PromptService $promptService, NoteService $noteService)
    {
        $this->categoryService = $categoryService;
        $this->promptService = $promptService;
        $this->noteService = $noteService;
    }

    /**
     * @Route("/category/create", name="api_create_category", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $bodyParameters = json_decode($request);
        $title = $bodyParameters->title;

        $category = $this->categoryService->create($title);
        return $this->json($category->jsonSerialize());
    }

    /**
     * @Route("/category/list", name="api_list_categories", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $categories = $this->categoryService->list();

        return $this->json($categories);
    }

    /**
     * @Route("/category/show/{id}", name="api_show_category", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->show($id);

        return $this->json($category->jsonSerialize(true));
    }

    /**
     * @Route("/category/update/{id}", name="api_update_category", methods={"PUT"})
     * @throws EntityNotFoundException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $bodyParameters = json_decode($request);
        $newTitle = $bodyParameters->title;
        $potentialNewPromptIds = $bodyParameters->potentialNewPrompts;
        $potentialNewNoteIds = $bodyParameters->potentialNewNotes;

        $promptObjects = [];
        foreach ($potentialNewPromptIds as $potentialNewPromptId) {
            $promptObjects += $this->promptService->show($potentialNewPromptId);
        }
        $noteObjects = [];
        foreach ($potentialNewNoteIds as $potentialNewNoteId) {
            $noteObjects += $this->noteService->show($potentialNewNoteId);
        }

        $updatedCategory = $this->categoryService->update($id, $newTitle, $promptObjects, $noteObjects);

        return $this->json($updatedCategory->jsonSerialize());
    }

    /**
     * @Route("/category/delete/{id}", name="api_delete_category", methods={"DELETE"})
     * @throws EntityNotFoundException
     */
    public function delete(int $id): JsonResponse
    {
        $this->categoryService->delete($id);
        $categoryHopefullyNull = $this->categoryService->show($id);
        return $this->json($categoryHopefullyNull->jsonSerialize());
    }
}
