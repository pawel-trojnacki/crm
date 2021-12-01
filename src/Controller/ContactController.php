<?php

namespace App\Controller;

use App\Constant\ContactConstant;
use App\Controller\Abstract\AbstractNoteController;
use App\Entity\Contact;
use App\Entity\ContactNote;
use App\Entity\Workspace;
use App\Form\ContactFormType;
use App\Form\NoteFormType;
use App\Repository\ContactNoteRepository;
use App\Repository\ContactRepository;
use App\Service\PagerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractNoteController
{
    public function __construct(
        private ContactRepository $contactRepository,
        private ContactNoteRepository $contactNoteRepository,
        private PagerService $pagerService,
    ) {
    }

    #[Route('{slug}/contacts/', name: 'app_contact_index', methods: ['GET'])]
    #[IsGranted('WORKSPACE_VIEW', subject: 'workspace')]
    public function index(Workspace $workspace, Request $request): Response
    {
        $currentPage = $request->query->get('page', 1);
        $order = $request->query->get('order');
        $search = $request->query->get('search');


        if (!is_numeric($currentPage)) {
            throw new NotFoundHttpException('Page not found');
        }

        $qb = $this->contactRepository->createFindByWorkspaceQueryBuilder(
            $workspace,
            $order,
            $search
        );

        $pager = $this->pagerService->createPager($qb, $currentPage, 25);

        return $this->render('/contact/index.html.twig', [
            'pager' => $pager,
            'order' => $order,
            'search' => $search,
            'sortOptions' => ContactConstant::SORT_OPTIONS,
        ]);
    }

    #[Route('/contact/{slug}', name: 'app_contact_show', methods: ['GET', 'POST'])]
    #[IsGranted('CONTACT_VIEW', subject: 'contact')]
    public function show(Contact $contact, Request $request): Response
    {
        $workspace = $contact->getWorkspace();

        $form = $this->createForm(NoteFormType::class, null, [
            'data_class' => ContactNote::class,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveNote($this->contactNoteRepository, $workspace, $form, $contact);

            return $this->redirectToRoute('app_contact_show', [
                'slug' => $contact->getSlug(),
            ]);
        }

        if ($request->isMethod('POST') && $request->request->get('delete-contact')) {
            $this->denyAccessUnlessGranted(
                'CONTACT_EDIT',
                $contact,
                'Current user is not authorized to delete this contact'
            );

            $this->contactRepository->delete($contact);

            $this->addFlashSuccess('Contact has been deleted');

            return $this->redirectToRoute('app_contact_index', [
                'slug' => $workspace->getSlug(),
            ]);
        }

        if ($request->isMethod('POST') && $request->request->get('delete-note')) {
            $this->deleteNote($this->contactNoteRepository, $request);

            return $this->redirectToRoute('app_contact_show', [
                'slug' => $contact->getSlug(),
            ]);
        }

        return $this->renderForm('contact/show.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('{slug}/contacts/create', name: 'app_contact_create', methods: ['GET', 'POST'])]
    #[IsGranted('WORKSPACE_ADD_ITEM', subject: 'workspace')]
    public function create(Workspace $workspace, Request $request): Response
    {
        $form = $this->createForm(type: ContactFormType::class, options: [
            'workspace' => $workspace,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Contact $contact */
            $contact = $form->getData();

            $user = $this->getUser();
            $contact->setCreator($user);

            $contact->setWorkspace($workspace);

            $this->contactRepository->save($contact);

            $referer = $request->request->get('referer');

            $this->addFlashSuccess('Contact has been created');

            return $this->redirectToReferer($referer, 'app_contact_index', [
                'slug' => $workspace->getSlug()
            ]);
        }

        return $this->renderForm('contact/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('contact/{slug}/edit', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    #[IsGranted('CONTACT_EDIT', subject: 'contact')]
    public function edit(Contact $contact, Request $request): Response
    {
        $workspace = $contact->getWorkspace();

        $form = $this->createForm(ContactFormType::class, $contact, [
            'workspace' => $workspace,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Contact $contact */
            $contact = $form->getData();

            $this->contactRepository->save($contact);

            $referer = $request->request->get('referer');

            $this->addFlashSuccess(sprintf(
                'Contact %s %s has been updated',
                $contact->getFirstName(),
                $contact->getLastName(),
            ));

            // Redirect to referer only if it is the index page - temporary solution
            if ($referer && strpos($referer, 'contacts')) {
                return $this->redirectToReferer($referer);
            }

            return $this->redirectToRoute('app_contact_show', [
                'slug' => $contact->getSlug(),
            ]);
        }

        return $this->renderForm('contact/edit.html.twig', [
            'form' => $form,
            'contact' => $contact,
        ]);
    }

    #[Route(
        '/contact/{slug}/edit-note/{id}',
        name: 'app_contact_edit_note',
        methods: ['GET', 'POST']
    )]
    public function editContactNote(string $slug, int $id, Request $request): Response
    {
        return $this->editNote(
            $id,
            $slug,
            $request,
            $this->contactNoteRepository,
            $this->contactRepository,
            ContactNote::class,
            'app_contact_show'
        );
    }
}
