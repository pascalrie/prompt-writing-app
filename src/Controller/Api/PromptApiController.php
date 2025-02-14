<?php

namespace App\Controller\Api;

use App\Enum\MessageOfResponse;
use App\Enum\TypeOfResponse;
use App\Service\CategoryService;
use App\Service\PromptService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PromptApiController extends BaseApiController
{
    /**
     * @var PromptService $promptService
     */
    protected PromptService $promptService;

    /**
     * @var CategoryService $categoryService
     */
    protected CategoryService $categoryService;

    /**
     * @param PromptService $promptService
     * @param CategoryService $categoryService
     * @param EntityManagerInterface $em
     */
    public function __construct(PromptService $promptService, CategoryService $categoryService, EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->promptService = $promptService;
        $this->categoryService = $categoryService;
    }

    /**
     * @Route("/prompt/create", name="api_create_prompt", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent());
        $title = $bodyParameters->title;
        $categoryTitle = $bodyParameters->category;

        $category = $this->categoryService->showByTitle($categoryTitle);
        if (null === $category) {
            $category = $this->categoryService->create($categoryTitle);
        }
        $prompt = $this->promptService->create($title, $category->getId());
        return $this->json($this->appendTimeStampToApiResponse($prompt->jsonSerialize()));
    }

    /**
     * @Route("/prompt/list", name="api_list_prompts", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $notes = $this->promptService->list();
        $response = [];
        foreach ($notes as $note) {
            $response += [$note->jsonSerialize()];
        }
        return $this->json($this->appendTimeStampToApiResponse($response));
    }

    /**
     * @Route("/prompt/show/{id}", name="api_show_prompt", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $prompt = $this->promptService->show($id);
        if (null === $prompt) {
            return $this->json($this->appendTimeStampToApiResponse(['code' => TypeOfResponse::NOT_FOUND, 'message' =>
                "Prompt with id: {$id}" . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING]));
        }
        return $this->json($this->appendTimeStampToApiResponse($prompt->jsonSerialize()));
    }

    /**
     * @Route("/prompt/update/{id}", name="api_update_prompt", methods={"PUT"})
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $bodyParameters = json_decode($request->getContent());
        if (null === $bodyParameters) {
            return $this->json($this->appendTimeStampToApiResponse([
                'message' => MessageOfResponse::NO_BODY_PARAMETERS
            ]));
        }
        $promptTitle = $bodyParameters->title;
        $categoryTitle = $bodyParameters->category;
        $newNotesJustToAdd = $bodyParameters->notes;

        $promptForUpdateShouldntBeNull = $this->promptService->show($id);

        if (null === $promptForUpdateShouldntBeNull) {
            return $this->json(['code' => TypeOfResponse::NOT_FOUND, 'message' => 'Prompt with id: '
                . $id . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING]);
        }

        $categoryTitle = $this->categoryService->showByTitle($categoryTitle);

        if (null === $categoryTitle) {
            return $this->json(['code' => TypeOfResponse::NOT_FOUND, 'message' => 'Category with title: '
                . $categoryTitle . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING]);
        }

        $prompt = $this->promptService->update($id, $promptTitle, $categoryTitle, $newNotesJustToAdd);
        return $this->json($this->appendTimeStampToApiResponse($prompt->jsonSerialize()));
    }

    /**
     * @Route("/prompt/delete/{id}", name="api_delete_prompt", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $promptForDeletionShouldntBeNull = $this->promptService->show($id);

        if (null === $promptForDeletionShouldntBeNull) {
            return $this->json($this->appendTimeStampToApiResponse(
                ['code' => TypeOfResponse::NOT_FOUND, 'message' => "Prompt for deletion with id: {$id}"
                    . MessageOfResponse::NOT_FOUND . MessageOfResponse::USE_EXISTING]));
        }

        $this->promptService->delete($id);
        $promptHopefullyNull = $this->promptService->show($id);
        if (null !== $promptHopefullyNull) {
            return $this->json($this->appendTimeStampToApiResponse(['message' => ["Deletion of Prompt with id: {$id}"
                . MessageOfResponse::NOT_SUCCESS . json_encode($promptHopefullyNull->jsonSerialize())]]));
        }

        return $this->json($this->appendTimeStampToApiResponse(['message' => "Deletion of Prompt with id: {$id}"
            . MessageOfResponse::SUCCESS]));
    }

    /**
     * @Route("/prompt/choose/random", name="api_prompt_choose", methods={"GET"})
     */
    public function chooseRandom(): JsonResponse
    {
        $randomPrompt = $this->promptService->showRandomPrompt();
        $randomPrompt = $randomPrompt->jsonSerialize();
        return $this->json($this->appendTimeStampToApiResponse($randomPrompt));
    }
}

