<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PromptApiController extends BaseApiController
{
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
        return $this->json(['Hallo Welt']);
    }

    /**
     * @Route("/prompt/show/{id}", name="api_show_prompt", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        return $this->json(['Hallo Welt']);
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
        return $this->json(['Hallo Welt']);
    }
}