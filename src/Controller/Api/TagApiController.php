<?php

namespace App\Controller\Api;

use App\Service\NoteService;
use App\Service\TagService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TagApiController extends BaseApiController
{
    /**
     * @var TagService $tagService
     */
    protected TagService $tagService;

    /**
     * @var NoteService $noteService
     */
    protected NoteService $noteService;

    /**
     * @param TagService $tagService
     * @param NoteService $noteService
     * @param EntityManagerInterface $em
     */
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
        $bodyParameters = json_decode($request->getContent());
        $title = $bodyParameters->title;
        $noteTitleList = $bodyParameters->noteTitles;
        $color = $bodyParameters->color;

        $createdTag = $this->tagService->create($title, $noteTitleList, $color);

        return $this->json($this->appendTimeStampToApiResponse($createdTag->jsonSerialize()));

    }

    /**
     * @Route("/tag/list", name="api_list_tags", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $tags = $this->tagService->list();
        $response = [];
        foreach ($tags as $tag) {
            $response += [$tag->jsonSerialize()];
        }
        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * @Route("/tag/show/{id}", name="api_show_tag", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $tag = $this->tagService->show($id);
        if (null === $tag) {
            return $this->json($this->appendTimeStampToApiResponse(['code' => 404, 'message' => 'Tag with id: ' . $id . ' not found.']));
        }
        return $this->json($this->appendTimeStampToApiResponse($tag->jsonSerialize()));
    }

    /**
     * @Route("/tag/update/{id}", name="api_update_tag", methods={"PUT"})
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent());
        $title = $bodyParameters->title;
        $color = $bodyParameters->color;
        $potentialNewNotes = $bodyParameters->notes;

        $noteObjects = [];
        foreach ($potentialNewNotes as $note) {
            $noteObjects += $this->noteService->show($note);
        }

        $tagToUpdate = $this->tagService->tagRepository->findBy(['title' => $title])[0];
        $notesFromDb = $tagToUpdate->getNotes()->toArray();
        $finalNotesToAdd = $this->findNonDuplicateObjectsInTwoArraysWithVariableCriteria($notesFromDb, $noteObjects);

        $finalTag = $this->tagService->update($id, $title, $finalNotesToAdd, $color);

        return $this->json($this->appendTimeStampToApiResponse($finalTag->jsonSerialize()));
    }

    /**
     * @Route("/tag/delete/{id}", name="api_delete_tag", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $tagForDeletionShouldntBeNull = $this->tagService->show($id);
        if (null === $tagForDeletionShouldntBeNull) {
            return $this->json($this->appendTimeStampToApiResponse(
                ['code' => 404, 'message' => "Tag for deletion with id: {$id} not found."]));
        }
        $this->tagService->delete($id);
        $tagAfterDeletionShouldBeNull = $this->tagService->show($id);
        if (null !== $tagAfterDeletionShouldBeNull) {
            return $this->json($this->appendTimeStampToApiResponse(
                ['message' => "Tag for deletion with id: {$id} was not successful."]
            ));
        }

        return $this->json($this->json($this->appendTimeStampToApiResponse(
            ['message' => "Tag for deletion with id: {$id} was successful."]
        )));
    }
}

