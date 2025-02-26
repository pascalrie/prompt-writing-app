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

    /**
     * FolderApiController constructor.
     *
     * @param FolderService $folderService The service responsible for handling folder-related operations.
     * @param EntityManagerInterface $em The entity manager for database interactions.
     */
    public function __construct(FolderService $folderService, EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->folderService = $folderService;
    }

    /**
     * Create a new folder.
     *
     * @Route("/api/folder/create", name="api_create_folder", methods={"POST"})
     *
     * @param Request $request The HTTP request containing the folder title.
     *
     * @return JsonResponse JSON response containing the created folder details or an error message.
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
     * Get the list of all folders.
     *
     * @Route("/api/folder/list", name="api_list_folders", methods={"GET"})
     *
     * @return JsonResponse JSON response containing a list of all folders.
     */
    public function list(): JsonResponse
    {
        $folders = $this->folderService->list();
        $response = array_map(fn($folder) => $folder->jsonSerialize(true), $folders);

        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * Retrieve a specific folder by its ID.
     *
     * @Route("/api/folder/show/{id}", name="api_show_folder", methods={"GET"})
     *
     * @param int $id The unique identifier of the folder to be retrieved.
     *
     * @return JsonResponse JSON response containing the folder details or an error message if not found.
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
     * Update the details of a specific folder.
     *
     * @Route("/api/folder/update/{id}", name="api_update_folder", methods={"PUT"})
     *
     * @param Request $request The HTTP request containing updated folder details.
     * @param int $id The unique identifier of the folder to be updated.
     *
     * @return JsonResponse JSON response containing the updated folder details or an error message.
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
     * Delete a specific folder by its ID.
     *
     * @Route("/api/folder/delete/{id}", name="api_delete_folder", methods={"DELETE"})
     *
     * @param int $id The unique identifier of the folder to be deleted.
     *
     * @return JsonResponse JSON response containing the result of the deletion operation.
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