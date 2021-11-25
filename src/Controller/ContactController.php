<?php

namespace App\Controller;

use App\Constant\ContactConstant;
use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Contact;
use App\Entity\ContactNote;
use App\Entity\Workspace;
use App\Form\ContactFormType;
use App\Form\ContactNoteFormType;
use App\Repository\ContactNoteRepository;
use App\Repository\ContactRepository;
use App\Service\PagerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractBaseController
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

        $qb = $this->contactRepository->createPagerQueryBuilder($workspace, $order, $search);

        $pager = $this->pagerService->createPager($qb, $currentPage, 25);

        return $this->render('/contact/index.html.twig', [
            'workspace' => $workspace,
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

        $form = $this->createForm(ContactNoteFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted(
                'WORKSPACE_ADD_ITEM',
                $workspace,
                'Current user is not authorized to create a note'
            );

            /** @var ContactNote $contactNote */
            $contactNote = $form->getData();

            $user = $this->getUser();

            $contactNote->setContact($contact);
            $contactNote->setCreator($user);

            $this->contactNoteRepository->save($contactNote);

            $this->addFlashSuccess('Note has been created');

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
                'slug' => $contact->getWorkspace()->getSlug(),
            ]);
        }

        if ($request->isMethod('POST') && $request->request->get('delete-note')) {
            $noteId = $request->request->get('delete-id');

            $contactNote = $this->contactNoteRepository->findOneBy(['id' => $noteId]);

            $this->denyAccessUnlessGranted(
                'CONTACT_NOTE_EDIT',
                $contactNote,
                'Current user is not authorized to delete this note',
            );

            $this->contactNoteRepository->delete($contactNote);

            $this->addFlashSuccess('Note has been deleted');

            return $this->redirectToRoute('app_contact_show', [
                'slug' => $contact->getSlug(),
            ]);
        }

        return $this->renderForm('contact/show.html.twig', [
            'contact' => $contact,
            'workspace' => $workspace,
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
            'workspace' => $workspace,
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
            'workspace' => $workspace,
            'contact' => $contact,
        ]);
    }

    #[Route(
        '/contact/{slug}/edit-note/{id}',
        name: 'app_contact_edit_note',
        methods: ['GET', 'POST']
    )]
    public function editNote(string $slug, int $id, Request $request): Response
    {
        $contact = $this->contactRepository->findOneBy(['slug' => $slug]);
        $workspace = $contact->getWorkspace();
        $contactNote = $this->contactNoteRepository->findOneBy(['id' => $id]);

        $this->denyAccessUnlessGranted(
            'CONTACT_NOTE_EDIT',
            $contactNote,
            'Current user is not authorized to edit this note'
        );

        $form = $this->createForm(ContactNoteFormType::class, $contactNote, [
            'label_text' => 'Edit note',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ContactNote $contactNote */
            $contactNote = $form->getData();

            $this->contactNoteRepository->save($contactNote);

            $this->addFlashSuccess('Note has been updated');

            return $this->redirectToRoute('app_contact_show', [
                'slug' => $slug,
            ]);
        }

        return $this->renderForm('contact-note/edit.html.twig', [
            'workspace' => $workspace,
            'contact' => $contact,
            'form' => $form,
        ]);
    }
}
