<?php

namespace App\Controller;

use App\Constant\CompanyConstant;
use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Company;
use App\Entity\Workspace;
use App\Form\CompanyFormType;
use App\Service\CompanyManager;
use App\Service\IndustryManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function index(Workspace $workspace, Request $request): Response
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

    #[Route('/{slug}/companies/create', name: 'app_company_create', methods: ['GET', 'POST'])]
    public function create(Workspace $workspace, Request $request): Response
    {
        $form = $this->createForm(CompanyFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->companyManager->save($form, $workspace);

            $referer = $request->request->get('referer');

            return $this->redirectToReferer($referer, 'app_comapny_index', [
                'slug' => $workspace->getSlug(),
            ]);
        }

        return $this->renderForm('company/create.html.twig', [
            'workspace' => $workspace,
            'form' => $form,
        ]);
    }

    #[Route('/company/{slug}/create', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(Company $company, Request $request): Response
    {
        $workspace = $company->getWorkspace();

        $form = $this->createForm(CompanyFormType::class, $company);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->companyManager->update($form);

            $referer = $request->request->get('referer');

            return $this->redirectToReferer($referer, 'app_comapny_index', [
                'slug' => $workspace->getSlug(),
            ]);
        }

        return $this->renderForm('company/edit.html.twig', [
            'workspace' => $workspace,
            'company' => $company,
            'form' => $form,
        ]);
    }
}
