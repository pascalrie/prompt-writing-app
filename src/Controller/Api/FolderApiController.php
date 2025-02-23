<?php

namespace App\Controller\Api;

use App\Enum\MessageOfResponse;
use App\Enum\TypeOfResponse;
use App\Service\FolderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FolderApiController extends BaseApiController
{
    protected FolderService $folderService;

    public function __construct(FolderService $folderService, EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->folderService = $folderService;
    }

    /**
     * @Route("/folder/create", name="api_create_folder", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent(), true);
        $title = $bodyParameters['title'] ?? null;

        if (!$title) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => MessageOfResponse::NO_BODY_PARAMETERS
            ]));
        }

        $folder = $this->folderService->create($title);

        return $this->json($this->appendTimeStampToApiResponse($folder->jsonSerialize()));
    }

    /**
     * @Route("/folder/list", name="api_list_folders", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $folders = $this->folderService->list();
        $response = array_map(fn($folder) => $folder->jsonSerialize(), $folders);

        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * @Route("/folder/show/{id}", name="api_show_folder", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $folder = $this->folderService->show($id);

        if (!$folder) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Folder with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        return $this->json($this->appendTimeStampToApiResponse($folder->jsonSerialize(true)));
    }

    /**
     * @Route("/folder/update/{id}", name="api_update_folder", methods={"PUT"})
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent(), true);

        if (!$bodyParameters) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => MessageOfResponse::NO_BODY_PARAMETERS
            ]));
        }

        $newTitle = $bodyParameters['title'] ?? null;
        $potentialNewNoteIds = $bodyParameters['potentialNewNotes'] ?? [];

        if (!$newTitle) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => "Title is missing."
            ]));
        }

        $folder = $this->folderService->show($id);
        if (!$folder) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Folder with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        $newNoteObjects = array_map(fn($noteId) => $this->folderService->show($noteId), $potentialNewNoteIds);

        $updatedFolder = $this->folderService->update($id, $newTitle, $newNoteObjects);

        return $this->json($this->appendTimeStampToApiResponse($updatedFolder->jsonSerialize()));
    }

    /**
     * @Route("/folder/delete/{id}", name="api_delete_folder", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $folder = $this->folderService->show($id);

        if (!$folder) {
            return $this->json($this->appendTimeStampToApiResponse([
                'code' => TypeOfResponse::NOT_FOUND,
                'message' => "Folder with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING
            ]));
        }

        $this->folderService->delete($id);

        if ($this->folderService->show($id)) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => "Deletion of Folder with id: {$id}" . MessageOfResponse::NOT_SUCCESS
            ]));
        }

        return $this->json($this->appendTimeStampToApiResponse([
            'message' => "Deletion of Folder with id: {$id}" . MessageOfResponse::SUCCESS
        ]));
    }
}