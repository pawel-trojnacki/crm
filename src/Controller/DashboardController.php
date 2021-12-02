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

        $activeDealsChart = $this->chartService->createActiveDealsChart($workspace);

        $activityChart = $this->chartService->createLastYearActivityChart($workspace);

        $industriesChart = $this->chartService->createPupularIndustriesChart($workspace);

        $latestNotes = $this->noteService->findLatestNotesByWorkspace($workspace);

        return $this->render('dashboard/index.html.twig', [
            'workspace' => $workspace,
            'record_numbers' => [
                'deal' => $dealNumber,
                'company' => $companyNumber,
                'contact' => $contactNumber,
            ],
            'active_deals_chart' => $activeDealsChart,
            'industries_chart' => $industriesChart,
            'activity_chart' => $activityChart,
            'latest_notes' => $latestNotes,
        ]);
    }
}
