<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Workspace;
use App\Repository\CompanyRepository;
use App\Repository\ContactRepository;
use App\Repository\DealRepository;
use App\Service\ChartService;
use App\Service\NoteService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractBaseController
{
    public function __construct(
        private DealRepository $dealRepository,
        private CompanyRepository $companyRepository,
        private ContactRepository $contactRepository,
        private ChartService $chartService,
        private NoteService $noteService,
    ) {
    }

    #[Route('/{slug}/dashboard', name: 'app_dashboard_index', methods: ['GET'])]
    public function index(Workspace $workspace): Response
    {
        $dealNumber = $this->dealRepository->findAllCountByWorkspace($workspace);
        $companyNumber = $this->companyRepository->findAllCountByWorkspace($workspace);
        $contactNumber = $this->contactRepository->findAllCountByWorkspace($workspace);

        $latestDeals = $this->dealRepository->findLatestByWorkspace($workspace);

        $chart = $this->chartService->createLastYearActivityChart($workspace);

        $activeDealsChart = $this->chartService->createActiveDealsChart($workspace);

        $latestNotes = $this->noteService->findLatestNotesByWorkspace($workspace);

        return $this->render('dashboard/index.html.twig', [
            'workspace' => $workspace,
            'record_numbers' => [
                'deal' => $dealNumber,
                'company' => $companyNumber,
                'contact' => $contactNumber,
            ],
            'latest_deals' => $latestDeals,
            'activity_chart' => $chart,
            'active_deals_chart' => $activeDealsChart,
            'latest_notes' => $latestNotes,
        ]);
    }
}
