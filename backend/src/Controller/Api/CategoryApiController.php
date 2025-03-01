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
                                NoteService     $noteService, EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->categoryService = $categoryService;
        $this->promptService = $promptService;
        $this->noteService = $noteService;
    }

    /**
     * Creates a new category entity.
     *
     * This endpoint accepts JSON-encoded body parameters, including the `title` of the new category.
     * If the `title` is not provided, a response with missing parameters is returned.
     *
     * @Route("/api/category/create", name="api_create_category", methods={"POST"})
     *
     * @param Request $request The HTTP request containing the category details.
     * @return JsonResponse The JSON response containing either the created category data or an error message.
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
     * Fetches a list of all categories.
     *
     * This endpoint returns an array of categories, where each category is represented
     * in a serialized JSON format.
     *
     * @Route("/api/category/list", name="api_list_categories", methods={"GET"})
     *
     * @return JsonResponse The JSON response containing a list of categories.
     */
    public function list(): JsonResponse
    {
        $categories = $this->categoryService->list();
        $response = array_map(fn($category) => $category->jsonSerialize(true), $categories);
        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * Displays details of a specific category by ID.
     *
     * If the category does not exist, a 404 error with an appropriate message is returned.
     *
     * @Route("/api/category/show/{id}", name="api_show_category", methods={"GET"})
     *
     * @param int $id The unique identifier of the category.
     * @return JsonResponse The JSON response containing the category details or an error message if not found.
     */
    public function show($id): JsonResponse
    {
        $id = intval($id);
        $category = $this->categoryService->show($id);

        if (!$category) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Category with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        return $this->json($this->appendTimeStampToApiResponse($category->jsonSerialize(true, true)));
    }

    /**
     * Updates an existing category by its ID.
     *
     * The update operation expects JSON-encoded body parameters such as `title`, `potentialNewPrompts`, and `potentialNewNotes`.
     * If the category is not found or if the body parameters are missing, an appropriate error message is returned.
     *
     * @Route("/api/category/update/{id}", name="api_update_category", methods={"PUT"})
     *
     * @param Request $request The HTTP request containing data to update the category.
     * @param int $id The unique identifier of the category to be updated.
     * @return JsonResponse The JSON response containing the updated category data or an error message.
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
     * Deletes a specific category by ID.
     *
     * If the category does not exist, a 404 error with an appropriate message is returned.
     * After deletion, a confirmation message is sent.
     *
     * @Route("/api/category/delete/{id}", name="api_delete_category", methods={"DELETE"})
     *
     * @param int $id The unique identifier of the category to be deleted.
     * @return JsonResponse The JSON response confirming deletion or providing an error message.
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
