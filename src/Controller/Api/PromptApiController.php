<?php

namespace App\Controller\Api;

use App\Service\PromptService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PromptApiController extends BaseApiController
{
    protected PromptService $promptService;

    public function __construct(PromptService $promptService) {
        $this->promptService = $promptService;
    }
    /**
     * @Route("/prompt/create", name="api_create_prompt", methods={"POST"})
     */
    public function create(): JsonResponse
    {
        return $this->json(['Hallo Welt']);
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
    public function update(int $id): JsonResponse
    {
        return $this->json(['Hallo Welt']);
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

        return $this->json('Hallo Welt');
    }
}