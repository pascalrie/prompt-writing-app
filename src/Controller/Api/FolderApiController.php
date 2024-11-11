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
    /**
     * @var FolderService $folderService
     */
    protected FolderService $folderService;

    /**
     * @param FolderService $folderService
     * @param EntityManagerInterface $em
     */
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
        $response = [];
        foreach ($folders as $folder) {
            $response += [$folder->jsonSerialize()];
        }
        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * @Route("/folder/show/{id}", name="api_show_folder", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $folder = $this->folderService->show($id);
        if (null === $folder) {
            return $this->json($this->appendTimeStampToApiResponse(['code' => TypeOfResponse::NOT_FOUND,
                'message' => 'Folder with id: ' . $id . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING]));
        }
        return $this->json($this->appendTimeStampToApiResponse($folder->jsonSerialize(true)));
    }

    /**
     * @Route("/folder/update/{id}", name="api_update_folder", methods={"PUT"})
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent());
        if (null === $bodyParameters) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => MessageOfResponse::NO_BODY_PARAMETERS
            ]));
        }
        $newTitle = $bodyParameters->title;
        $potentialNewNoteIds = $bodyParameters->potentialNewNotes;

        $folder = $this->folderService->show($id);

        if (null === $folder) {
            return $this->json($this->appendTimeStampToApiResponse(['code' => TypeOfResponse::NOT_FOUND, 'message' =>
                'Folder with id: ' . $id . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING]));
        }

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
        $folderForDeletionShouldntBeNull = $this->folderService->show($id);

        if (null === $folderForDeletionShouldntBeNull) {
            return $this->json($this->appendTimeStampToApiResponse(
                ['code' => TypeofResponse::NOT_FOUND, 'message' => "Folder for deletion with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING]));
        }

        $this->folderService->delete($id);

        $folderHopefullyNull = $this->folderService->show($id);
        if (null !== $folderHopefullyNull) {
            return $this->json($this->appendTimeStampToApiResponse(['message' => "Deletion of Folder with {$id}" . MessageOfResponse::NOT_SUCCESS]));
        }

        return $this->json($this->appendTimeStampToApiResponse(['message' => "Deletion of Folder with {$id}" . MessageOfResponse::SUCCESS]));
    }
}

