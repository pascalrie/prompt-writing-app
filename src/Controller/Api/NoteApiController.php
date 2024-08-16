<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class NoteApiController extends BaseApiController
{
    /**
     * @Route("/note/create", name="api_create_note", methods={"POST"})
     */
    public function create(): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }

    /**
     * @Route("/note/list", name="api_list_notes", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }

    /**
     * @Route("/note/show/{id}", name="api_show_note", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }

    /**
     * @Route("/note/update/{id}", name="api_update_note", methods={"PUT"})
     */
    public function update(int $id): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }

    /**
     * @Route("/note/delete/{id}", name="api_delete_note", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }
}
