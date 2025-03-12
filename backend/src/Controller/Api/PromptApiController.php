<?php

namespace App\Controller\Api;

use App\Enum\MessageOfResponse;
use App\Enum\TypeOfResponse;
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

    /**
     * Constructor for PromptApiController.
     *
     * @param PromptService $promptService The service handling prompts.
     * @param CategoryService $categoryService The service handling categories.
     * @param EntityManagerInterface $em The entity manager interface.
     */
    public function __construct(PromptService $promptService, CategoryService $categoryService, EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->promptService = $promptService;
        $this->categoryService = $categoryService;
    }

    /**
     * Create a new prompt.
     *
     * @Route("/api/prompt/create", name="api_create_prompt", methods={"POST"})
     *
     * @param Request $request The HTTP request object.
     * @return JsonResponse The JSON response containing the created prompt data or an error message.
     */
    public function create(Request $request): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent(), true);

        $title = $bodyParameters['title'] ?? null;
        $categoryTitle = $bodyParameters['category'] ?? null;

        if (!$title || !$categoryTitle) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => MessageOfResponse::NO_BODY_PARAMETERS
            ]));
        }

        $category = $this->categoryService->showByTitle($categoryTitle)
            ?? $this->categoryService->create($categoryTitle);

        $prompt = $this->promptService->create($title, $category->getId());

        return $this->json($this->appendTimeStampToApiResponse($prompt->jsonSerialize()));
    }

    /**
     * List all prompts.
     *
     * @Route("/api/prompt/list", name="api_list_prompts", methods={"GET"})
     *
     * @return JsonResponse A JSON response containing a list of all prompts.
     */
    public function list(): JsonResponse
    {
        $prompts = $this->promptService->list();
        $response = array_map(fn($prompt) => $prompt->jsonSerialize(), $prompts);

        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * Show the details of a specific prompt by ID.
     *
     * @Route("/api/prompt/show/{id}", name="api_show_prompt", methods={"GET"})
     *
     * @param int $id The ID of the prompt to display.
     * @return JsonResponse A JSON response containing the prompt data or an error message if not found.
     */
    public function show(int $id): JsonResponse
    {
        $prompt = $this->promptService->show($id);

        if (!$prompt) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Prompt with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        return $this->json($this->appendTimeStampToApiResponse($prompt->jsonSerialize()));
    }

    /**
     * Update an existing prompt.
     *
     * @Route("/api/prompt/update/{id}", name="api_update_prompt", methods={"PUT"})
     *
     * @param Request $request The HTTP request object.
     * @param int $id The ID of the prompt to update.
     * @return JsonResponse A JSON response containing the updated prompt data or an error message if not found.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $prompt = $this->promptService->show($id);

        if (!$prompt) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Prompt with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        $bodyParameters = json_decode($request->getContent(), true);

        if (!$bodyParameters) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => MessageOfResponse::NO_BODY_PARAMETERS
            ]));
        }

        $title = $bodyParameters['title'] ?? $prompt->getTitle();
        $categoryTitle = $bodyParameters['category'] ?? null;
        $notes = $bodyParameters['notes'] ?? [];

        $category = $this->categoryService->showByTitle($categoryTitle);

        if (!$category) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Category with title: {$categoryTitle}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        $updatedPrompt = $this->promptService->update($id, $title, $category, $notes);

        return $this->json($this->appendTimeStampToApiResponse($updatedPrompt->jsonSerialize()));
    }

    /**
     * Delete an existing prompt by ID.
     *
     * @Route("/api/prompt/delete/{id}", name="api_delete_prompt", methods={"DELETE"})
     *
     * @param int $id The ID of the prompt to delete.
     * @return JsonResponse A JSON response indicating success or failure of the deletion process.
     */
    public function delete(int $id): JsonResponse
    {
        $prompt = $this->promptService->show($id);

        if (!$prompt) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Prompt with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        $this->promptService->delete($id);

        if ($this->promptService->show($id)) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => "Deletion of Prompt with id: {$id}" . MessageOfResponse::NOT_SUCCESS
            ]));
        }

        return $this->json($this->appendTimeStampToApiResponse([
            'message' => "Deletion of Prompt with id: {$id}" . MessageOfResponse::SUCCESS
        ]));
    }

    /**
     * Select a random prompt.
     *
     * @Route("/api/prompt/choose/random", name="api_prompt_choose", methods={"GET"})
     * @return JsonResponse A JSON response containing a randomly selected prompt.
     * @throws \Exception
     *
     */
    public function chooseRandom(): JsonResponse
    {
        $randomPrompt = $this->promptService->showRandom();

        return $this->json($this->appendTimeStampToApiResponse($randomPrompt->jsonSerialize()));
    }
}