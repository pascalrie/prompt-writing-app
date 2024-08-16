<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagApiController extends BaseApiController
{
    /**
     * @Route("/tag/create", name="api_create_tag", methods={"GET", "POST"})
     */
    public function create(): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }

    /**
     * @Route("/tag/list", name="api_list_tags", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }

    /**
     * @Route("/tag/show/{id}", name="api_show_tag", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }

    /**
     * @Route("/tag/update/{id}", name="api_update_tag", methods={"PUT"})
     */
    public function update(int $id): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }

    /**
     * @Route("/tag/delete/{id}", name="api_delete_tag", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        return $this->json(['Hallo Welt']);
    }
}
