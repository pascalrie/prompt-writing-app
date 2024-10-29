<?php

namespace App\Controller\Api;

use App\Service\CategoryService;
use App\Service\NoteService;
use App\Service\PromptService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryApiController extends BaseApiController
{
    /**
     * @var CategoryService $categoryService
     */
    protected CategoryService $categoryService;

    /**
     * @var PromptService $promptService
     */
    protected PromptService $promptService;

    /**
     * @var NoteService $noteService
     */
    protected NoteService $noteService;

    /**
     * @param CategoryService $categoryService
     * @param PromptService $promptService
     * @param NoteService $noteService
     * @param EntityManagerInterface $em
     */
    public function __construct(CategoryService        $categoryService, PromptService $promptService, NoteService $noteService,
                                EntityManagerInterface $em)
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
        $bodyParameters = json_decode($request->getContent());
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
        $response = [];
        foreach ($categories as $category) {
            $response += [$category->jsonSerialize()];
        }
        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * @Route("/category/show/{id}", name="api_show_category", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->show($id);
        if (null === $category) {
            return $this->json($this->appendTimeStampToApiResponse(['code' => 404, 'message' => "Category with id: {$id} not found."]));
        }
        return $this->json($this->appendTimeStampToApiResponse($category->jsonSerialize(true)));
    }

    /**
     * @Route("/category/update/{id}", name="api_update_category", methods={"PUT"})
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent());
        $newTitle = $bodyParameters->title;
        $potentialNewPromptIds = $bodyParameters->potentialNewPrompts;
        $potentialNewNoteIds = $bodyParameters->potentialNewNotes;

        $categoryForUpdateShouldntBeNull = $this->categoryService->show($id);
        if (null === $categoryForUpdateShouldntBeNull) {
            return $this->json($this->appendTimeStampToApiResponse(
                ['code' => 404, 'message' => "Category for update with id: {$id} not found."]));
        }

        $promptObjectsForGivenIds = [];
        foreach ($potentialNewPromptIds as $potentialNewPromptId) {
            $promptObjectsForGivenIds += $this->promptService->show($potentialNewPromptId);
        }

        $noteObjectsForGivenIds = [];
        foreach ($potentialNewNoteIds as $potentialNewNoteId) {
            $noteObjectsForGivenIds += $this->noteService->show($potentialNewNoteId);
        }

        $categoryInDb = $this->categoryService->findBy(['id' => $id])[0];
        $updatedCategory = $this->categoryService->update($id, $newTitle, $promptObjectsForGivenIds, $noteObjectsForGivenIds);

        return $this->json($this->appendTimeStampToApiResponse($updatedCategory->jsonSerialize()));
    }

    /**
     * @Route("/category/delete/{id}", name="api_delete_category", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $categoryForDeletionShouldntBeNull = $this->categoryService->show($id);

        if (null === $categoryForDeletionShouldntBeNull) {
            return $this->json($this->appendTimeStampToApiResponse(
                ['code' => 404, 'message' => "Category for deletion with id: {$id} not found."]));
        }

        $this->categoryService->delete($id);
        $categoryHopefullyNull = $this->categoryService->show($id);
        if (null !== $categoryHopefullyNull) {
            return $this->json($this->appendTimeStampToApiResponse(['message' => ['Not successfully deleted category' . json_encode($categoryHopefullyNull->jsonSerialize())]]));
        }
        return $this->json($this->appendTimeStampToApiResponse(['message' => 'Successfully deleted category.']));
    }
}

