<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
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

        $qb = $this->dealRepository->createFindByWorkspaceQueryBuilder($workspace);

        $pager = $this->pagerService->createPager($qb, $currentPage, 12);

        return $this->render('deal/index.html.twig', [
            'workspace' => $workspace,
            'pager' => $pager,
        ]);
    }
}
