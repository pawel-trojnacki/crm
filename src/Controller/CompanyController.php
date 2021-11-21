<?php

namespace App\Controller;

use App\Constant\CompanyConstant;
use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Company;
use App\Entity\Contact;
use App\Entity\Workspace;
use App\Form\CompanyFormType;
use App\Service\CompanyManager;
use App\Service\ContactManager;
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
        private ContactManager $contactManager,
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

    #[Route('/company/{slug}', name: 'app_company_show', methods: ['GET', 'POST'])]
    public function show(Company $company, Request $request): Response
    {
        $workspace = $company->getWorkspace();

        if ($request->isMethod('POST') && $request->request->get('delete-company')) {

            if ($request->request->get('delete-contacts') !== null) {
                /** @var Contact[] $contacts */
                $contacts = $company->getContacts();

                foreach ($contacts as $contact) {
                    $this->contactManager->delete($contact);
                }
            }

            $this->companyManager->delete($company);

            return $this->redirectToRoute('app_company_index', [
                'slug' => $company->getWorkspace()->getSlug(),
            ]);
        }

        return $this->render('company/show.html.twig', [
            'workspace' => $workspace,
            'company' => $company,
        ]);
    }

    #[Route('/{slug}/companies/create', name: 'app_company_create', methods: ['GET', 'POST'])]
    public function create(Workspace $workspace, Request $request): Response
    {
        $form = $this->createForm(CompanyFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Company $company */
            $company = $form->getData();

            $user = $this->getUser();

            $this->companyManager->save($company, $workspace, $user);

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

    #[Route('/company/{slug}/edit', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(Company $company, Request $request): Response
    {
        $workspace = $company->getWorkspace();

        $form = $this->createForm(CompanyFormType::class, $company);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Company $company */
            $company = $form->getData();

            $this->companyManager->update($company);

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
