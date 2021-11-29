<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Workspace;
use App\Repository\DealRepository;
use App\Service\ChartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractBaseController
{
    public function __construct(
        private ChartService $chartService,
        private DealRepository $dealRepository,
    ) {
    }

    #[Route('/{slug}/dashboard', name: 'app_dashboard_index', methods: ['GET'])]
    public function index(Workspace $workspace): Response
    {
        dd($this->dealRepository->findCountByCreateMonth($workspace));
        $chart = $this->chartService->createEntityCountChart($workspace);

        return $this->render('dashboard/index.html.twig', [
            'workspace' => $workspace,
            'chart' => $chart,
        ]);
    }
}
