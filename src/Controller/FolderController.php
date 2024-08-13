<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FolderController extends AbstractController
{
    /**
     * @Route("/folder", name="app_folder")
     */
    public function index(): Response
    {
        return $this->render('folder/index.html.twig', [
            'controller_name' => 'FolderController',
        ]);
    }
}
