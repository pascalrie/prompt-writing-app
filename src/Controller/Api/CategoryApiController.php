<?php

namespace App\Controller\Api;

use App\Enum\MessageOfResponse;
use App\Enum\TypeOfResponse;
use App\Service\CategoryService;
use App\Service\NoteService;
use App\Service\PromptService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryApiController extends BaseApiController
{
    private CategoryService $categoryService;

    private PromptService $promptService;

    private NoteService $noteService;

    public function __construct(CategoryService $categoryService, PromptService $promptService,
                                NoteService $noteService, EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->categoryService = $categoryService;
        $this->promptService = $promptService;
        $this->noteService = $noteService;
    }

    /**
     * @Route("/category/create", name="api_create_category", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent(), true);

        if (!isset($bodyParameters['title'])) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => MessageOfResponse::NO_BODY_PARAMETERS
            ]));
        }

        $category = $this->categoryService->create($bodyParameters['title']);

        return $this->json($this->appendTimeStampToApiResponse($category->jsonSerialize()));
    }

    /**
     * @Route("/category/list", name="api_list_categories", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $categories = $this->categoryService->list();
        $response = array_map(fn($category) => $category->jsonSerialize(), $categories);

        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * @Route("/category/show/{id}", name="api_show_category", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->show($id);

        if (!$category) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Category with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        return $this->json($this->appendTimeStampToApiResponse($category->jsonSerialize(true)));
    }

    /**
     * @Route("/category/update/{id}", name="api_update_category", methods={"PUT"})
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent(), true);

        if (!$bodyParameters) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => MessageOfResponse::NO_BODY_PARAMETERS
            ]));
        }

        $category = $this->categoryService->show($id);

        if (!$category) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Category for update with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        $promptIds = $bodyParameters['potentialNewPrompts'] ?? [];
        $noteIds = $bodyParameters['potentialNewNotes'] ?? [];

        $prompts = array_map(fn($promptId) => $this->promptService->show($promptId), $promptIds);
        $notes = array_map(fn($noteId) => $this->noteService->show($noteId), $noteIds);

        $updatedCategory = $this->categoryService->update($id, $bodyParameters['title'], $prompts, $notes);

        return $this->json($this->appendTimeStampToApiResponse($updatedCategory->jsonSerialize()));
    }

    /**
     * @Route("/category/delete/{id}", name="api_delete_category", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $category = $this->categoryService->show($id);

        if (!$category) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Category for deletion with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        $this->categoryService->delete($id);

        if ($this->categoryService->show($id)) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => "Deletion of Category " . MessageOfResponse::NOT_SUCCESS
            ]));
        }

        return $this->json($this->appendTimeStampToApiResponse([
            'message' => "Deletion of Category with id: {$id}" . MessageOfResponse::SUCCESS
        ]));
    }
}
