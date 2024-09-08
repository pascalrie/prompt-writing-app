<?php

namespace App\Controller\Api;

use App\Entity\Note;
use App\Repository\Factory\RepositoryCreator;
use App\Service\CategoryService;
use App\Service\NoteService;
use App\Service\TagService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NoteApiController extends AbstractApiController
{
    protected NoteService $noteService;

    protected TagService $tagService;

    protected CategoryService $categoryService;

    public function __construct(NoteService     $noteService, TagService $tagService,
                                CategoryService $categoryService,  EntityManagerInterface $em, RepositoryCreator $repositoryCreator)
    {
        parent::__construct($em, $repositoryCreator);

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

        $tagsInArrayCollection = new ArrayCollection();
        $categoryForCreation = $this->categoryService->showByTitle($category);
        if (null === $categoryForCreation) {
            $categoryForCreation = $this->categoryService->create($category);
        }

        foreach ($tagTitles as $tagTitle) {
            $tagFromDb = $this->tagService->showBy('title', $tagTitle);
            if (null === $tagFromDb) {
                $newTag = $this->tagService->create($tagTitle);
                $tagsInArrayCollection->add($newTag);
            } else {
                $tagsInArrayCollection->add($tagFromDb);
            }
        }

        $note = $this->noteService->create($title, $content, $tagsInArrayCollection, $categoryForCreation);
        return $this->json($this->appendTimeStampToApiResponse($note->jsonSerialize()));
    }

    /**
     * @Route("/note/list", name="api_list_notes", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $notes = $this->noteService->list();
        $response = [];
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
            return $this->json($this->appendTimeStampToApiResponse(['code' => 404, 'message' => "Note with id: {$id} not found."]));
        }
        return $this->json($this->appendTimeStampToApiResponse($note->jsonSerialize()));
    }

    /**
     * @Route("/note/update/{id}", name="api_update_note", methods={"PUT"})
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent());
        $content = $bodyParameters->content;
        $newTitle = $bodyParameters->title;
        $newCategory = $bodyParameters->category;
        $newTagTitles = $bodyParameters->tags;
        $contentIsAdded = $bodyParameters->contentIsAdded;

        $noteForUpdateShouldntBeNull = $this->noteService->show($id);
        if (null === $noteForUpdateShouldntBeNull) {
            return $this->json($this->appendTimeStampToApiResponse(
                ['code' => 404, 'message' => "Note for update with id: {$id} not found."]));
        }

        if (null === $contentIsAdded) {
            $contentIsAdded = false;
        }

        // check for wish to remove tags with "/rm" or "/remove" && check for tag-duplicates with current note
        $newTagTitles = $this->adjustTagList($noteForUpdateShouldntBeNull, $newTagTitles);

        $this->noteService->update($id, $content, $contentIsAdded, $newTitle, $newTagTitles, $newCategory);
        $updatedNoteFromDb = $this->noteService->show($id);

        return $this->json($this->appendTimeStampToApiResponse($updatedNoteFromDb->jsonSerialize()));
    }

    /**
     * @param Note $note
     * @param array|null $newTagTitles
     * @return array
     */
    private function adjustTagList(Note $note, ?array $newTagTitles): array
    {
        $listOfTags = [];
        foreach ($note->getTags()->toArray() as $tag) {
            $listOfTags += $tag;
        }
        // find items that are not in intersection of both arrays
        $tagResults = $this->findDisjunctItemsInTwoArraysBasedOnTitle($listOfTags, $newTagTitles);
        if (!empty($tagResults)) {
            foreach ($tagResults as $tag)
                if (str_contains($tag->getTitle(), '/remove' || str_contains($tag->getTitle(), '/rm'))) {
                    $this->tagService->removeFromNote($note, $tag);
                    $this->noteService->removeTagFromNote($note, $tag);
                }
            return $tagResults;
        }
        return $newTagTitles;
    }

    /**
     * @Route("/note/delete/{id}", name="api_delete_note", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $noteForDeletionShouldntBeNull = $this->noteService->show($id);
        if (null === $noteForDeletionShouldntBeNull) {
            return $this->json($this->appendTimeStampToApiResponse(
                ['code' => 404, 'message' => "Note for deletion with id: {$id} not found."]));
        }
        $this->noteService->delete($id);
        $noteAfterDeletionShouldBeNull = $this->noteService->show($id);
        if (null !== $noteAfterDeletionShouldBeNull) {
            return $this->json($this->appendTimeStampToApiResponse(
                ['message' => "Note deletion with id: {$id} was not successful."]
            ));
        }
        $this->responseForEachImplementedController(Note::class);
        return $this->json($this->json($this->appendTimeStampToApiResponse(
            ['message' => "Note deletion with id: {$id} was successful."]
        )));
    }
}