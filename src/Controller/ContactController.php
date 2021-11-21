<?php

namespace App\Controller;

use App\Constant\ContactConstant;
use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Contact;
use App\Entity\ContactNote;
use App\Entity\Workspace;
use App\Form\ContactFormType;
use App\Form\ContactNoteFormType;
use App\Service\ContactManager;
use App\Service\ContactNoteManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ContactController extends AbstractBaseController
{
    public function __construct(
        private ContactManager $contactManager,
        private ContactNoteManager $contactNoteManager,
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

        $pager = $this->contactManager->createPager($workspace, $currentPage, $order, $search);

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

            $this->contactNoteManager->save($contactNote, $contact, $user);

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

            $this->contactManager->delete($contact);

            return $this->redirectToRoute('app_contact_index', [
                'slug' => $contact->getWorkspace()->getSlug(),
            ]);
        }

        if ($request->isMethod('POST') && $request->request->get('delete-note')) {
            $noteId = $request->request->get('delete-id');

            $contactNote = $this->contactNoteManager->findOneById($noteId);

            $this->denyAccessUnlessGranted(
                'CONTACT_NOTE_EDIT',
                $contactNote,
                'Current user is not authorized to delete this note',
            );

            $this->contactNoteManager->deleteById($noteId);

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

            $this->contactManager->save($contact, $workspace, $user);

            $referer = $request->request->get('referer');

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

            $this->contactManager->update($contact);

            $referer = $request->request->get('referer');

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
        $contact = $this->contactManager->findOneBySlug($slug);
        $workspace = $contact->getWorkspace();
        $contactNote = $this->contactNoteManager->findOneById($id);

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

            $this->contactNoteManager->update($contactNote);

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
