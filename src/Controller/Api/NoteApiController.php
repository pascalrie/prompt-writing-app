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
    /**
     * @var NoteService $noteService
     */
    protected NoteService $noteService;

    /**
     * @var TagService $tagService
     */
    protected TagService $tagService;

    /**
     * @var CategoryService $categoryService
     */
    protected CategoryService $categoryService;

    /**
     * @param NoteService $noteService
     * @param TagService $tagService
     * @param CategoryService $categoryService
     * @param EntityManagerInterface $em
     */
    public function __construct(NoteService     $noteService, TagService $tagService,
                                CategoryService $categoryService, EntityManagerInterface $em)
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
        $bodyParameters = json_decode($request->getContent());
        $title = $bodyParameters->title;
        $content = $bodyParameters->content;
        $category = $bodyParameters->category;
        $tagTitles = $bodyParameters->tags;

        $categoryForCreation = $this->categoryService->create($category);

        $tagsInArrayCollection = new ArrayCollection();
        foreach ($tagTitles as $tagTitle) {
            $tagsInArrayCollection->add($this->createTagIfNonExistentByTitle($tagTitle));
        }

        $note = $this->noteService->create($title, $content, $tagsInArrayCollection, $categoryForCreation);
        return $this->json($this->appendTimeStampToApiResponse($note->jsonSerialize()));
    }

    private function createTagIfNonExistentByTitle(string $title): Tag
    {
        $oldTag = $this->tagService->showBy('title', $title);
        if (null === $oldTag) {
            $oldTag = $this->tagService->create($title);
        }
        return $oldTag;
    }

    /**
     * @Route("/note/list", name="api_list_notes", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $notes = $this->noteService->list();
        $response = [];
        /** @var Note $note */
        foreach ($notes as $note) {
            $response += [$note->jsonSerialize()];
        }
        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * @Route("/note/show/{id}", name="api_show_note", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $note = $this->noteService->show($id);

        if (null === $note) {
            return $this->json($this->appendTimeStampToApiResponse(['code' => TypeOfResponse::NOT_FOUND,
                'message' => 'Note with id: ' . $id . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING]));

        }

        return $this->json($this->appendTimeStampToApiResponse($note->jsonSerialize()));
    }

    /**
     * @Route("/note/update/{id}", name="api_update_note", methods={"PUT"})
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $noteForUpdateShouldntBeNull = $this->noteService->show($id);

        if (null === $noteForUpdateShouldntBeNull) {
            return $this->json($this->appendTimeStampToApiResponse(
                ['code' => TypeOfResponse::NOT_FOUND, 'message' => "Note for update with id: {$id}"
                    . MessageOfResponse::NOT_FOUND
                    . MessageOfResponse::USE_EXISTING]));
        }

        $bodyParameters = json_decode($request->getContent());
        if (null === $bodyParameters) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => MessageOfResponse::NO_BODY_PARAMETERS
            ]));
        }
        $content = $bodyParameters->content;
        $newTitle = $bodyParameters->title;
        $newCategoryTitle = $bodyParameters->category;
        $newTagTitles = $bodyParameters->tags;
        $contentIsAdded = $bodyParameters->contentIsAdded;
        $contentShouldBeRemoved = $bodyParameters->contentShouldBeRemoved;

        if (null === $contentIsAdded) {
            $contentIsAdded = false;
        }

        $updatedNoteFromDb = $this->noteService->update($id, $newTitle, $content, $contentIsAdded,
            $contentShouldBeRemoved, $newTagTitles, $newCategoryTitle);

        return $this->json($this->appendTimeStampToApiResponse($updatedNoteFromDb->jsonSerialize()));
    }

    /**
     * @Route("/note/delete/{id}", name="api_delete_note", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $noteForDeletionShouldntBeNull = $this->noteService->show($id);
        if (null === $noteForDeletionShouldntBeNull) {
            return $this->json($this->appendTimeStampToApiResponse(
                ['code' => TypeOfResponse::NOT_FOUND,
                    'message' => "Note for deletion with id: {$id}" . MessageOfResponse::NOT_FOUND
                    . MessageOfResponse::USE_EXISTING]));
        }
        $this->noteService->delete($id);
        $noteAfterDeletionShouldBeNull = $this->noteService->show($id);
        if (null !== $noteAfterDeletionShouldBeNull) {
            return $this->json($this->appendTimeStampToApiResponse(
                ['message' => "Note deletion with id: {$id}" . MessageOfResponse::NOT_SUCCESS]
            ));
        }

        return $this->json($this->json($this->appendTimeStampToApiResponse(
            ['message' => "Note deletion with id: {$id}" . MessageOfResponse::SUCCESS]
        )));
    }
}
