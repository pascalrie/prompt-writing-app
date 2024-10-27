<?php

namespace App\Controller\Admin\Dashboard;

use App\Entity\Category;
use App\Entity\Folder;
use App\Entity\Note;
use App\Entity\Prompt;
use App\Entity\Tag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Html');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Category', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Folder', 'fas fa-list', Folder::class);
        yield MenuItem::linkToCrud('Note', 'fas fa-list', Note::class);
        yield MenuItem::linkToCrud('Prompt', 'fas fa-list', Prompt::class);
        yield MenuItem::linkToCrud('Tag', 'fas fa-list', Tag::class);
    }
}
