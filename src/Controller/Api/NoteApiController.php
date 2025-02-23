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

/**
 * Class NoteApiController
 *
 * This controller handles API requests for managing notes, including creating, listing,
 * showing, updating, and deleting notes. It also manages associated tags and categories.
 */
class NoteApiController extends BaseApiController
{
    /**
     * @var NoteService Handles note-related business logic.
     */
    protected NoteService $noteService;

    /**
     * @var TagService Handles tag-related business logic.
     */
    protected TagService $tagService;

    /**
     * @var CategoryService Handles category-related business logic.
     */
    protected CategoryService $categoryService;

    /**
     * Constructor for NoteApiController.
     *
     * @param NoteService $noteService Note service for performing operations on notes.
     * @param TagService $tagService Tag service for handling tags.
     * @param CategoryService $categoryService Category service for managing categories.
     * @param EntityManagerInterface $em Doctrine's entity manager for database operations.
     */
    public function __construct(NoteService $noteService, TagService $tagService, CategoryService $categoryService, EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->noteService = $noteService;
        $this->tagService = $tagService;
        $this->categoryService = $categoryService;
    }

    /**
     * Creates a new note.
     *
     * @Route("/note/create", name="api_create_note", methods={"POST"})
     *
     * @param Request $request The HTTP request containing the note data in JSON format.
     *
     * @return JsonResponse The created note data or an error message if creation failed.
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
     * Lists all notes.
     *
     * @Route("/note/list", name="api_list_notes", methods={"GET"})
     *
     * @return JsonResponse A JSON response containing a list of all notes.
     */
    public function list(): JsonResponse
    {
        $notes = $this->noteService->list();
        $response = array_map(fn(Note $note) => $note->jsonSerialize(), $notes);

        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * Retrieves a specific note by ID.
     *
     * @Route("/note/show/{id}", name="api_show_note", methods={"GET"})
     *
     * @param int $id The ID of the note to retrieve.
     *
     * @return JsonResponse The note details or an error message if the note does not exist.
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
     * Updates a specific note by ID.
     *
     * @Route("/note/update/{id}", name="api_update_note", methods={"PUT"})
     *
     * @param Request $request The HTTP request containing updated note data.
     * @param int $id The ID of the note to update.
     *
     * @return JsonResponse The updated note data or an error message if the note does not exist or the update failed.
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
     * Deletes a specific note by ID.
     *
     * @Route("/note/delete/{id}", name="api_delete_note", methods={"DELETE"})
     *
     * @param int $id The ID of the note to delete.
     *
     * @return JsonResponse A success or error message indicating the result of the deletion operation.
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

    /**
     * Creates a new tag or retrieves an existing tag by its title.
     *
     * @param string $title The title of the tag to create or fetch.
     *
     * @return Tag The created or existing tag entity.
     */
    private function createTagIfNonExistentByTitle(string $title): Tag
    {
        return $this->tagService->showOneBy('title', $title)
            ?? $this->tagService->create($title);
    }
}
