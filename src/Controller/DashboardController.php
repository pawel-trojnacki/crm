<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Workspace;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractBaseController
{

    #[Route('/{slug}/dashboard', name: 'app_dashboard_index', methods: ['GET'])]
    public function index(Workspace $workspace): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'workspace' => $workspace,
        ]);
    }
}
