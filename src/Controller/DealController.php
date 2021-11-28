<?php

namespace App\Controller;

use App\Constant\BaseSortConstant;
use App\Controller\Abstract\AbstractNoteController;
use App\Entity\Deal;
use App\Entity\DealNote;
use App\Entity\Workspace;
use App\Form\DealFormType;
use App\Form\NoteFormType;
use App\Repository\DealNoteRepository;
use App\Repository\DealRepository;
use App\Service\PagerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DealController extends AbstractNoteController
{
    public function __construct(
        private DealRepository $dealRepository,
        private DealNoteRepository $dealNoteRepository,
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

    #[Route('/deal/{slug}', name: 'app_deal_show', methods: ['GET', 'POST'])]
    #[IsGranted('DEAL_VIEW', subject: 'deal')]
    public function show(Deal $deal, Request $request): Response
    {
        $workspace = $deal->getWorkspace();

        $form = $this->createForm(NoteFormType::class, null, [
            'data_class' => DealNote::class,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveNote($this->dealNoteRepository, $workspace, $form, $deal);

            return $this->redirectToRoute('app_deal_show', [
                'slug' => $deal->getSlug(),
            ]);
        }

        if ($request->isMethod('POST') && $request->request->get('delete-note')) {
            $this->deleteNote($this->dealNoteRepository, $request);

            return $this->redirectToRoute('app_deal_show', [
                'slug' => $deal->getSlug(),
            ]);
        }

        if ($request->isMethod('POST') && $request->request->get('delete-deal')) {
            $this->denyAccessUnlessGranted(
                'DEAL_EDIT',
                $deal,
                'Current user is not authorized to delete this deal'
            );

            $this->dealRepository->delete($deal);

            $this->addFlashSuccess('Deal has been deleted');

            return $this->redirectToRoute('app_deal_index', [
                'slug' => $workspace->getSlug(),
            ]);
        }

        return $this->renderForm('deal/show.html.twig', [
            'workspace' => $workspace,
            'deal' => $deal,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/deals/create', name: 'app_deal_create', methods: ['GET', 'POST'])]
    #[IsGranted('WORKSPACE_EDIT', subject: 'workspace')]
    public function create(Workspace $workspace, Request $request): Response
    {
        $form = $this->createForm(DealFormType::class, null, [
            'workspace' => $workspace,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Deal $deal */
            $deal = $form->getData();

            $deal->setWorkspace($workspace);
            $deal->setCreator($this->getUser());

            $this->dealRepository->save($deal);

            $this->addFlashSuccess('Deal has been created');

            return $this->redirectToRoute('app_deal_show', [
                'slug' => $deal->getSlug(),
            ]);
        }

        return $this->renderForm('deal/create.html.twig', [
            'workspace' => $workspace,
            'form' => $form,
        ]);
    }

    #[Route('/deal/{slug}/edit', name: 'app_deal_edit', methods: ['GET', 'POST'])]
    #[IsGranted('DEAL_EDIT', subject: 'deal')]
    public function edit(Deal $deal, Request $request): Response
    {
        $workspace = $deal->getWorkspace();

        $form = $this->createForm(DealFormType::class, $deal, [
            'workspace' => $workspace,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()  && $form->isValid()) {
            /** @var Deal $deal */
            $deal = $form->getData();

            $this->dealRepository->save($deal);

            $this->addFlashSuccess(sprintf(
                'Deal %s has been updated',
                $deal->getName(),
            ));

            return $this->redirectToRoute('app_deal_show', [
                'slug' => $deal->getSlug(),
            ]);
        }

        return $this->renderForm('deal/edit.html.twig', [
            'workspace' => $workspace,
            'deal' => $deal,
            'form' => $form,
        ]);
    }

    #[Route(
        '/deal/{slug}/edit-note/{id}',
        name: 'app_deal_edit_note',
        methods: ['GET', 'POST']
    )]
    public function editDealNote(string $slug, int $id, Request $request): Response
    {
        return $this->editNote(
            $id,
            $slug,
            $request,
            $this->dealNoteRepository,
            $this->dealRepository,
            DealNote::class,
            'app_deal_show',
        );
    }
}
