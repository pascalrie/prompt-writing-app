<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FolderApiController extends BaseApiController
{
    /**
     * @Route("/folder/create", name="api_create_folder", methods={"POST"})
     */
    public function create(): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }

    /**
     * @Route("/folder/list", name="api_list_folders", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }

    /**
     * @Route("/folder/show/{id}", name="api_show_folder", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }

    /**
     * @Route("/folder/update/{id}", name="api_update_folder", methods={"PUT"})
     */
    public function update(int $id): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }

    /**
     * @Route("/folder/delete/{id}", name="api_delete_folder", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }
}
