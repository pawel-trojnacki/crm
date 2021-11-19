<?php

namespace App\Controller;

use App\Constant\CompanyConstant;
use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Workspace;
use App\Service\CompanyManager;
use App\Service\IndustryManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractBaseController
{
    public function __construct(
        private CompanyManager $companyManager,
        private IndustryManager $industryManager,
    ) {
    }

    #[Route('/{slug}/companies', name: 'app_company_index', methods: ['GET'])]
    public function index(Workspace $workspace, Request $request): HttpFoundationResponse
    {
        $industries = $this->industryManager->findAllAlphabetically();

        $currentPage = $request->query->get('page', 1);
        $search = $request->query->get('search');
        $industry = $request->query->get('industry');
        $order = $request->query->get('order');

        if (!is_numeric($currentPage)) {
            throw new NotFoundHttpException('Page not found');
        }

        $pager = $this->companyManager->createPager(
            $workspace,
            $currentPage,
            $search,
            $industry,
            $order
        );

        return $this->render('company/index.html.twig', [
            'workspace' => $workspace,
            'industries' => $industries,
            'pager' => $pager,
            'search' => $search,
            'industry' => $industry,
            'order' => $order,
            'sortOptions' => CompanyConstant::SORT_OPTIONS,
        ]);
    }
}
