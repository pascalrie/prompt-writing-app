<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PromptController extends AbstractController
{
    /**
     * @Route("/prompt", name="app_prompt")
     */
    public function index(): Response
    {
        return $this->render('prompt/index.html.twig', [
            'controller_name' => 'PromptController',
        ]);
    }
}
