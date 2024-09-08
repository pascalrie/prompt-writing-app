<?php

namespace App\Controller\Api;

use App\Repository\Factory\RepositoryCreator;
use App\Service\CategoryService;
use App\Service\NoteService;
use App\Service\PromptService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryApiController extends AbstractApiController
{
    protected CategoryService $categoryService;

    protected PromptService $promptService;

    protected NoteService $noteService;

    public function __construct(CategoryService $categoryService, PromptService $promptService, NoteService $noteService,
                                EntityManagerInterface $em, RepositoryCreator $repositoryCreator)
    {
        parent::__construct($em, $repositoryCreator);
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
