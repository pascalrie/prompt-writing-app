<?php

namespace App\Controller\Api;

use App\Service\CategoryService;
use App\Service\NoteService;
use App\Service\TagService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NoteApiController extends BaseApiController
{
    protected NoteService $noteService;

    protected TagService $tagService;

    protected CategoryService $categoryService;

    public function __construct(NoteService     $noteService, TagService $tagService,
                                CategoryService $categoryService)
    {
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
