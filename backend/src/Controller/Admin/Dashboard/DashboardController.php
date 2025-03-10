<?php

namespace App\Controller\Admin\Dashboard;

use App\Controller\Admin\NoteCrudController;
use App\Entity\Prompt;
use App\Entity\Category;
use App\Entity\Folder;
use App\Entity\Note;
use App\Entity\Tag;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }
    /**
     * @Route("/admin", name="admin")
     */

    public function index(): Response
    {
        $url = $this->adminUrlGenerator
            ->setController(NoteCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Prompt-Writing Application');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Content Management');

        yield MenuItem::linkToCrud('Prompts', 'fa fa-lightbulb', Prompt::class);
        yield MenuItem::linkToCrud('Categories', 'fa fa-folder', Category::class);
        yield MenuItem::linkToCrud('Folders', 'fa fa-folder-open', Folder::class);
        yield MenuItem::linkToCrud('Notes', 'fa fa-sticky-note', Note::class);
        yield MenuItem::linkToCrud('Tags', 'fa fa-tags', Tag::class);
    }
}