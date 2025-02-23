<?php

namespace App\Controller\Api;

use App\Entity\Note;
use App\Entity\Tag;
use App\Enum\MessageOfResponse;
use App\Enum\TypeOfResponse;
use App\Service\CategoryService;
use App\Service\NoteService;
use App\Service\TagService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NoteApiController extends BaseApiController
{
    protected NoteService $noteService;
    protected TagService $tagService;
    protected CategoryService $categoryService;

    public function __construct(NoteService $noteService, TagService $tagService, CategoryService $categoryService, EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->noteService = $noteService;
        $this->tagService = $tagService;
        $this->categoryService = $categoryService;
    }

    /**
     * @Route("/note/create", name="api_create_note", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent(), true);

        $title = $bodyParameters['title'] ?? null;
        $content = $bodyParameters['content'] ?? null;
        $category = $bodyParameters['category'] ?? null;
        $tagTitles = $bodyParameters['tags'] ?? [];

        if (!$title || !$content || !$category) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => MessageOfResponse::NO_BODY_PARAMETERS
            ]));
        }

        $categoryEntity = $this->categoryService->create($category);

        $tagEntities = array_map(
            fn($tagTitle) => $this->createTagIfNonExistentByTitle($tagTitle),
            $tagTitles
        );

        $note = $this->noteService->create($title, $content, new ArrayCollection($tagEntities), $categoryEntity);

        return $this->json($this->appendTimeStampToApiResponse($note->jsonSerialize()));
    }

    /**
     * @Route("/note/list", name="api_list_notes", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $notes = $this->noteService->list();
        $response = array_map(fn(Note $note) => $note->jsonSerialize(), $notes);

        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * @Route("/note/show/{id}", name="api_show_note", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $note = $this->noteService->show($id);

        if (!$note) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Note with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        return $this->json($this->appendTimeStampToApiResponse($note->jsonSerialize()));
    }

    /**
     * @Route("/note/update/{id}", name="api_update_note", methods={"PUT"})
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $existingNote = $this->noteService->show($id);

        if (!$existingNote) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Note with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        $bodyParameters = json_decode($request->getContent(), true);

        if (!$bodyParameters) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => MessageOfResponse::NO_BODY_PARAMETERS
            ]));
        }

        $title = $bodyParameters['title'] ?? $existingNote->getTitle();
        $content = $bodyParameters['content'] ?? $existingNote->getContent();
        $categoryTitle = $bodyParameters['category'] ?? null;
        $tagTitles = $bodyParameters['tags'] ?? [];
        $contentIsAdded = $bodyParameters['contentIsAdded'] ?? false;
        $contentShouldBeRemoved = $bodyParameters['contentShouldBeRemoved'] ?? false;

        $updatedNote = $this->noteService->update(
            $id,
            $title,
            $content,
            $contentIsAdded,
            $contentShouldBeRemoved,
            $tagTitles,
            $categoryTitle
        );

        return $this->json($this->appendTimeStampToApiResponse($updatedNote->jsonSerialize()));
    }

    /**
     * @Route("/note/delete/{id}", name="api_delete_note", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $note = $this->noteService->show($id);

        if (!$note) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Note with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        $this->noteService->delete($id);

        if ($this->noteService->show($id)) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => "Note deletion with id: {$id}" . MessageOfResponse::NOT_SUCCESS
            ]));
        }

        return $this->json($this->appendTimeStampToApiResponse([
            'message' => "Note deletion with id: {$id}" . MessageOfResponse::SUCCESS
        ]));
    }
    
    private function createTagIfNonExistentByTitle(string $title): Tag
    {
        return $this->tagService->showOneBy('title', $title)
            ?? $this->tagService->create($title);
    }
}
