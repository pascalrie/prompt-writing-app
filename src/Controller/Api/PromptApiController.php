<?php

namespace App\Controller\Api;

use App\Service\CategoryService;
use App\Service\PromptService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PromptApiController extends BaseApiController
{
    protected PromptService $promptService;

    protected CategoryService $categoryService;

    public function __construct(PromptService     $promptService, CategoryService $categoryService, EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->promptService = $promptService;
        $this->categoryService = $categoryService;
    }

    /**
     * @Route("/prompt/create", name="api_create_prompt", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent());
        $title = $bodyParameters->title;
        $categoryTitle = $bodyParameters->category;

        $category = $this->categoryService->showByTitle($categoryTitle);
        if (null === $category) {
            $category = $this->categoryService->create($categoryTitle);
        }
        $prompt = $this->promptService->create($title, $category->getId());
        return $this->json($this->appendTimeStampToApiResponse($prompt->jsonSerialize()));
    }

    /**
     * @Route("/prompt/list", name="api_list_prompts", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $notes = $this->promptService->list();
        $response = [];
        foreach ($notes as $note) {
            $response += [$note->jsonSerialize()];
        }
        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * @Route("/prompt/show/{id}", name="api_show_prompt", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $prompt = $this->promptService->show($id);
        if (null === $prompt) {
            return $this->json($this->appendTimeStampToApiResponse(['code' => 404, 'message' => 'Prompt with id: ' . $id . ' not found.']));
        }
        return $this->json($this->appendTimeStampToApiResponse($prompt->jsonSerialize()));
    }

    /**
     * @Route("/prompt/update/{id}", name="api_update_prompt", methods={"PUT"})
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent());
        $promptTitle = $bodyParameters->title;
        $categoryTitle = $bodyParameters->category;

        $promptForUpdateShouldntBeNull = $this->promptService->show($id);

        if (null === $promptForUpdateShouldntBeNull) {
            return $this->json(['code' => 404, 'message' => 'Prompt with id: ' . $id . ' not found.']);
        }

        $categoryTitle = $this->categoryService->showByTitle($categoryTitle);

        if (null === $categoryTitle) {
            return $this->json(['code' => 404, 'message' => 'Category with title: ' . $categoryTitle . ' not found. 
            Please create one.']);
        }

        $prompt = $this->promptService->update($id, $promptTitle, $categoryTitle);
        return $this->json($this->appendTimeStampToApiResponse($prompt->jsonSerialize()));
    }

    /**
     * @Route("/prompt/delete/{id}", name="api_delete_prompt", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $promptForDeletionShouldntBeNull = $this->promptService->show($id);

        if (null === $promptForDeletionShouldntBeNull) {
            return $this->json($this->appendTimeStampToApiResponse(
                ['code' => 404, 'message' => "Prompt for deletion with id: {$id} not found."]));
        }

        $this->promptService->delete($id);
        $promptHopefullyNull = $this->promptService->show($id);
        if (null !== $promptHopefullyNull) {
            return $this->json($this->appendTimeStampToApiResponse(['message' => ['Not successfully deleted prompt' . json_encode($promptHopefullyNull->jsonSerialize())]]));
        }

        return $this->json($this->appendTimeStampToApiResponse(['message' => 'Successfully deleted prompt.']));
    }
}