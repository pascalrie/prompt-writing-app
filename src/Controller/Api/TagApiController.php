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

    /**
     * Constructor for TagApiController.
     *
     * @param TagService $tagService The service layer for managing tags.
     * @param NoteService $noteService The service layer for managing notes.
     * @param EntityManagerInterface $em The Doctrine EntityManager instance.
     */
    public function __construct(TagService $tagService, NoteService $noteService, EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->tagService = $tagService;
        $this->noteService = $noteService;
    }

    /**
     * Create a new tag.
     *
     * @Route("/tag/create", name="api_create_tag", methods={"POST"})
     *
     * @param Request $request The HTTP request object containing the JSON payload.
     *
     * @return JsonResponse The created tag as a JSON response, or an error message if the creation fails.
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
     * List all existing tags.
     *
     * @Route("/tag/list", name="api_list_tags", methods={"GET"})
     *
     * @return JsonResponse A JSON response containing a list of all tags.
     */
    public function list(): JsonResponse
    {
        $tags = $this->tagService->list();
        $response = array_map(fn($tag) => $tag->jsonSerialize(), $tags);

        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * Show details of a specific tag by ID.
     *
     * @Route("/tag/show/{id}", name="api_show_tag", methods={"GET"})
     *
     * @param int $id The ID of the tag to retrieve.
     *
     * @return JsonResponse The requested tag data as a JSON response, or an error message if not found.
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
     * Update an existing tag by ID.
     *
     * @Route("/tag/update/{id}", name="api_update_tag", methods={"PUT"})
     *
     * @param Request $request The HTTP request object containing the JSON payload for the update.
     * @param int $id The ID of the tag to update.
     *
     * @return JsonResponse The updated tag as a JSON response, or an error message if the update fails.
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
     * Delete an existing tag by ID.
     *
     * @Route("/tag/delete/{id}", name="api_delete_tag", methods={"DELETE"})
     *
     * @param int $id The ID of the tag to delete.
     *
     * @return JsonResponse A confirmation message as a JSON response, or an error message if the deletion fails.
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