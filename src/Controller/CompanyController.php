<?php

namespace App\Controller;

use App\Constant\BaseSortConstant;
use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Company;
use App\Entity\Contact;
use App\Entity\Workspace;
use App\Form\CompanyFormType;
use App\Repository\CompanyRepository;
use App\Repository\ContactRepository;
use App\Repository\IndustryRepository;
use App\Service\CsvService;
use App\Service\FilterService;
use App\Service\PagerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractBaseController
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private IndustryRepository $industryRepository,
        private ContactRepository $contactRepository,
        private PagerService $pagerService,
        private FilterService $filterService,
        private CsvService $csvService,
    ) {
    }

    #[Route('/{slug}/companies', name: 'app_company_index', methods: ['GET'])]
    #[IsGranted('WORKSPACE_VIEW', subject: 'workspace')]
    public function index(Workspace $workspace, Request $request): Response
    {
        $industries = $this->industryRepository->findAllAlphabetically();

        $currentPage = $this->filterService->getCurrentPage($request);
        $search = $this->filterService->getSearch($request);
        $order = $this->filterService->getOrder($request);
        $industry = $this->filterService->getIndustry($request);
        $selectedUserId = $this->filterService->getUserIdBySlugParam($request);

        $qb = $this->companyRepository->createFindByWorskpaceQueryBuilder(
            $workspace,
            $search,
            $industry,
            $selectedUserId,
            $order
        );

        $pager = $this->pagerService->createPager($qb, $currentPage);

        return $this->render('company/index.html.twig', [
            'industries' => $industries,
            'pager' => $pager,
            'search' => $search,
            'industry' => $industry,
            'order' => $order,
            'sortOptions' => BaseSortConstant::SORT_OPTIONS,
            'team_members' => $this->filterService->findTeamMembersByWorkspace($workspace),
            'selected_user_id' => $selectedUserId,
        ]);
    }

    #[Route('/company/{slug}', name: 'app_company_show', methods: ['GET', 'POST'])]
    #[IsGranted('COMPANY_VIEW', subject: 'company')]
    public function show(Company $company, Request $request): Response
    {
        if ($request->isMethod('POST') && $request->request->get('delete-company')) {

            $this->denyAccessUnlessGranted(
                'COMPANY_EDIT',
                $company,
                'Current user is not authorized to delete this company'
            );

            if ($request->request->get('delete-contacts') !== null) {
                /** @var Contact[] $contacts */
                $contacts = $company->getContacts();

                foreach ($contacts as $contact) {
                    $this->contactRepository->delete($contact);
                }
            }

            $this->companyRepository->delete($company);

            $this->addFlashSuccess('Company has been deleted');

            return $this->redirectToRoute('app_company_index', [
                'slug' => $company->getWorkspace()->getSlug(),
            ]);
        }

        return $this->render('company/show.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/{slug}/companies/create', name: 'app_company_create', methods: ['GET', 'POST'])]
    #[IsGranted('WORKSPACE_ADD_ITEM', subject: 'workspace')]
    public function create(Workspace $workspace, Request $request): Response
    {
        $form = $this->createForm(CompanyFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Company $company */
            $company = $form->getData();

            $user = $this->getUser();
            $company->setCreator($user);

            $company->setWorkspace($workspace);

            $this->companyRepository->save($company);

            $referer = $request->request->get('referer');

            $this->addFlashSuccess('Company has been created');

            return $this->redirectToReferer($referer, 'app_comapny_index', [
                'slug' => $workspace->getSlug(),
            ]);
        }

        return $this->renderForm('company/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/company/{slug}/edit', name: 'app_company_edit', methods: ['GET', 'POST'])]
    #[IsGranted('COMPANY_EDIT', subject: 'company')]
    public function edit(Company $company, Request $request): Response
    {
        $form = $this->createForm(CompanyFormType::class, $company);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Company $company */
            $company = $form->getData();

            $this->companyRepository->save($company);

            $referer = $request->request->get('referer');

            $this->addFlashSuccess(sprintf(
                'Company %s has been updated',
                $company->getName()
            ));

            // Redirect to referer only if it is the index page - temporary solution
            if ($referer && strpos($referer, 'companies')) {
                return $this->redirectToReferer($referer);
            }

            return $this->redirectToRoute('app_company_show', [
                'slug' => $company->getSlug(),
            ]);
        }

        return $this->renderForm('company/edit.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/companies/csv', name: 'app_company_csv', methods: ['GET'])]
    #[IsGranted('WORKSPACE_VIEW', subject: 'workspace')]
    public function outputCsv(Workspace $workspace): Response
    {
        $companies = $this->companyRepository->findBy(['workspace' => $workspace]);

        $csvData = $this->csvService->getCsvCompanies($companies);

        $response = new Response($csvData);

        return $this->csvService->returnCsvResponse($response, 'companies');
    }
}
