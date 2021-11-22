<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Workspace;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractBaseController
{
    #[Route('/{slug}/team', name: 'app_team_index', methods: ['GET'])]
    public function index(Workspace $workspace): Response
    {
        return $this->render('team/index.html.twig', [
            'workspace' => $workspace,
        ]);
    }
}
