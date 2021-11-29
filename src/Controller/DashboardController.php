<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Workspace;
use App\Repository\CompanyRepository;
use App\Repository\ContactRepository;
use App\Repository\DealRepository;
use App\Service\ChartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractBaseController
{
    public function __construct(
        private ChartService $chartService,
        private DealRepository $dealRepository,
        private CompanyRepository $companyRepository,
        private ContactRepository $contactRepository,
    ) {
    }

    #[Route('/{slug}/dashboard', name: 'app_dashboard_index', methods: ['GET'])]
    public function index(Workspace $workspace): Response
    {
        $dealNumber = $this->dealRepository->findAllCountByWorkspace($workspace);
        $companyNumber = $this->companyRepository->findAllCountByWorkspace($workspace);
        $contactNumber = $this->contactRepository->findAllCountByWorkspace($workspace);

        $chart = $this->chartService->createLastYearActivityChart($workspace);

        return $this->render('dashboard/index.html.twig', [
            'workspace' => $workspace,
            'chart' => $chart,
            'record_numbers' => [
                'deal' => $dealNumber,
                'company' => $companyNumber,
                'contact' => $contactNumber,
            ]
        ]);
    }
}
