<?php

namespace App\Controller;

use App\Constant\BaseSortConstant;
use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Deal;
use App\Entity\Workspace;
use App\Repository\DealRepository;
use App\Service\PagerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DealController extends AbstractBaseController
{
    public function __construct(
        private DealRepository $dealRepository,
        private PagerService $pagerService,
    ) {
    }

    #[Route('/{slug}/deals', name: 'app_deal_index', methods: 'GET')]
    #[IsGranted('WORKSPACE_VIEW', subject: 'workspace')]
    public function index(Workspace $workspace, Request $request): Response
    {
        $currentPage = $request->query->get('page', 1);
        $search = $request->query->get('search');
        $order = $request->query->get('order');

        $stageId = $request->query->get('stageId');

        $stage = is_numeric($stageId) ? Deal::STAGES[(int) $stageId - 1] : null;

        $qb = $this->dealRepository->createFindByWorkspaceQueryBuilder(
            $workspace,
            $this->getUser(),
            $search,
            $stage,
            $order
        );

        $pager = $this->pagerService->createPager($qb, $currentPage, 12);

        return $this->render('deal/index.html.twig', [
            'workspace' => $workspace,
            'pager' => $pager,
            'search' => $search,
            'stage' => $stageId,
            'order' => $order,
            'sortOptions' => BaseSortConstant::SORT_OPTIONS,
            'stages' => Deal::STAGES,
        ]);
    }

    #[Route('/deal/{slug}', name: 'app_deal_show', methods: ['GET'])]
    #[IsGranted('DEAL_VIEW', subject: 'deal')]
    public function show(Deal $deal): Response
    {
        $workspace = $deal->getWorkspace();

        return $this->render('deal/show.html.twig', [
            'workspace' => $workspace,
            'deal' => $deal,
        ]);
    }
}
