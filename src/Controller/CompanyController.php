<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Workspace;
use App\Service\CompanyManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractBaseController
{
    public function __construct(
        private CompanyManager $companyManager,
    ) {
    }

    #[Route('/{slug}/companies', name: 'app_company_index', methods: ['GET'])]
    public function index(Workspace $workspace, Request $request): HttpFoundationResponse
    {
        $currentPage = $request->query->get('page', 1);

        if (!is_numeric($currentPage)) {
            throw new NotFoundHttpException('Page not found');
        }

        $pager = $this->companyManager->createPager($workspace, $currentPage);

        return $this->render('company/index.html.twig', [
            'workspace' => $workspace,
            'pager' => $pager,
        ]);
    }
}
