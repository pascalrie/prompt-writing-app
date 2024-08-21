<?php

namespace App\Controller\Api;

use App\Service\FolderService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FolderApiController extends BaseApiController
{
    protected FolderService $folderService;

    public function __construct(FolderService $folderService)
    {
        $this->folderService = $folderService;
    }

    /**
     * @Route("/folder/create", name="api_create_folder", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent());
        $title = $bodyParameters->title;

        $folder = $this->folderService->create($title);

        return $this->json($this->appendTimeStampToApiResponse($folder->jsonSerialize()));
    }

    /**
     * @Route("/folder/list", name="api_list_folders", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $folders = $this->folderService->list();
        return $this->json($this->appendTimeStampToApiResponse($folders));
    }

    /**
     * @Route("/folder/show/{id}", name="api_show_folder", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $folder = $this->folderService->show($id);
        return $this->json($this->appendTimeStampToApiResponse($folder->jsonSerialize(true)));
    }

    /**
     * @Route("/folder/update/{id}", name="api_update_folder", methods={"PUT"})
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent());
        $newTitle = $bodyParameters->title;
        $potentialNewNoteIds = $bodyParameters->potentialNewNotes;

        $potentialNewNoteObjects = [];
        foreach ($potentialNewNoteIds as $potentialNewNoteId) {
            $potentialNewNoteObjects += $this->folderService->show($potentialNewNoteId);
        }

        $folder = $this->folderService->update($id, $newTitle, $potentialNewNoteObjects);

        return $this->json($this->appendTimeStampToApiResponse($folder->jsonSerialize()));
    }

    /**
     * @Route("/folder/delete/{id}", name="api_delete_folder", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $this->folderService->delete($id);
        $folderHopefullyNull = $this->folderService->show($id);
        if (null !== $folderHopefullyNull) {
            return $this->json($this->appendTimeStampToApiResponse(['message' => 'Failed.']));
        }

        return $this->json($this->appendTimeStampToApiResponse(['message' => 'Success.']));
    }
}
