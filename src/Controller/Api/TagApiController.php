<?php

namespace App\Controller\Api;

use App\Enum\MessageOfResponse;
use App\Enum\TypeOfResponse;
use App\Service\NoteService;
use App\Service\TagService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TagApiController extends BaseApiController
{
    protected TagService $tagService;
    protected NoteService $noteService;

    public function __construct(TagService $tagService, NoteService $noteService, EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->tagService = $tagService;
        $this->noteService = $noteService;
    }

    /**
     * @Route("/tag/create", name="api_create_tag", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent(), true);

        $title = $bodyParameters['title'] ?? null;
        $noteTitles = $bodyParameters['noteTitles'] ?? [];
        $color = $bodyParameters['color'] ?? null;

        if (!$title) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => MessageOfResponse::NO_BODY_PARAMETERS
            ]));
        }

        $createdTag = $this->tagService->create($title, $noteTitles, $color);

        return $this->json($this->appendTimeStampToApiResponse($createdTag->jsonSerialize()));
    }

    /**
     * @Route("/tag/list", name="api_list_tags", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $tags = $this->tagService->list();
        $response = array_map(fn($tag) => $tag->jsonSerialize(), $tags);

        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * @Route("/tag/show/{id}", name="api_show_tag", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $tag = $this->tagService->show($id);

        if (!$tag) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Tag with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        return $this->json($this->appendTimeStampToApiResponse($tag->jsonSerialize()));
    }

    /**
     * @Route("/tag/update/{id}", name="api_update_tag", methods={"PUT"})
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $tag = $this->tagService->show($id);

        if (!$tag) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Tag with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        $bodyParameters = json_decode($request->getContent(), true);

        if (!$bodyParameters) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => MessageOfResponse::NO_BODY_PARAMETERS
            ]));
        }

        $title = $bodyParameters['title'] ?? $tag->getTitle();
        $color = $bodyParameters['color'] ?? $tag->getColor();
        $noteTitles = $bodyParameters['notes'] ?? [];

        $noteEntities = array_map(fn($noteTitle) => $this->noteService->show($noteTitle), $noteTitles);

        $updatedTag = $this->tagService->update($id, $title, $noteEntities, $color);

        return $this->json($this->appendTimeStampToApiResponse($updatedTag->jsonSerialize()));
    }

    /**
     * @Route("/tag/delete/{id}", name="api_delete_tag", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $tag = $this->tagService->show($id);

        if (!$tag) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Tag with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        $this->tagService->delete($id);

        if ($this->tagService->show($id)) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => "Tag with id: {$id} could not be deleted" . MessageOfResponse::NOT_SUCCESS
            ]));
        }

        return $this->json($this->appendTimeStampToApiResponse([
            'message' => "Tag with id: {$id} successfully deleted" . MessageOfResponse::SUCCESS
        ]));
    }
}