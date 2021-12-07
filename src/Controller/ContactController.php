<?php

namespace App\Controller;

use App\Constant\ContactConstant;
use App\Controller\Abstract\AbstractBaseController;
use App\Dto\NoteDto;
use App\Entity\Contact;
use App\Entity\ContactNote;
use App\Entity\Workspace;
use App\Form\ContactFormType;
use App\Form\NoteFormType;
use App\Repository\ContactNoteRepository;
use App\Repository\ContactRepository;
use App\Service\CsvService;
use App\Service\FilterService;
use App\Service\PagerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractBaseController
{
    public function __construct(
        private ContactRepository $contactRepository,
        private ContactNoteRepository $contactNoteRepository,
        private PagerService $pagerService,
        private FilterService $filterService,
        private CsvService $csvService,
    ) {
    }

    #[Route('{slug}/contacts/', name: 'app_contact_index', methods: ['GET'])]
    #[IsGranted('WORKSPACE_VIEW', subject: 'workspace')]
    public function index(Workspace $workspace, Request $request): Response
    {
        $currentPage = $this->filterService->getCurrentPage($request);
        $search = $this->filterService->getSearch($request);
        $order = $this->filterService->getOrder($request);

        $selectedUserId = $this->filterService->getUserIdBySlugParam($request);

        $qb = $this->contactRepository->createFindByWorkspaceQueryBuilder(
            $workspace,
            $search,
            $selectedUserId,
            $order,
        );

        $pager = $this->pagerService->createPager($qb, $currentPage, 25);

        return $this->render('/contact/index.html.twig', [
            'pager' => $pager,
            'order' => $order,
            'search' => $search,
            'sortOptions' => ContactConstant::SORT_OPTIONS,
            'team_members' => $this->filterService->findTeamMembersByWorkspace($workspace),
            'selected_user_id' => $selectedUserId,
        ]);
    }

    #[Route('/contact/{slug}', name: 'app_contact_show', methods: ['GET', 'POST'])]
    #[IsGranted('CONTACT_VIEW', subject: 'contact')]
    public function show(Contact $contact, Request $request): Response
    {
        $workspace = $contact->getWorkspace();

        $form = $this->createForm(NoteFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted(
                'WORKSPACE_ADD_ITEM',
                $workspace,
                'Current user is not authorized to create a note'
            );

            /** @var NoteDto $note */
            $noteDto = $form->getData();

            $note = ContactNote::createFromDto($contact, $this->getUser(), $noteDto);

            $this->contactNoteRepository->save($note);

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
                'slug' => $workspace->getSlug(),
            ]);
        }

        if ($request->isMethod('POST') && $request->request->get('delete-note')) {
            $noteId = $request->request->get('delete-id');

            $note = $this->contactNoteRepository->findOneBy(['id' => $noteId]);

            $this->denyAccessUnlessGranted(
                'NOTE_EDIT',
                $note,
                'Current user is not authorized to delete this note',
            );

            $this->contactNoteRepository->delete($note);

            $this->addFlashSuccess('Note has been deleted');

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
    public function editContactNote(string $slug, string $id, Request $request): Response
    {
        $contact = $this->contactRepository->findOneBy(['slug' => $slug]);
        $note = $this->contactNoteRepository->findOneBy(['id' => $id]);

        $this->denyAccessUnlessGranted(
            'NOTE_EDIT',
            $note,
            'Current user is not authorized to edit this note'
        );

        $noteDto = NoteDto::createFromNoteEntity($note);

        $form = $this->createForm(NoteFormType::class, $noteDto, [
            'label_text' => 'Edit note',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var NoteDto $noteDto */
            $noteDto = $form->getData();

            $note->updateFromDto($noteDto);

            $this->contactNoteRepository->save($note);

            $this->addFlashSuccess('Note has been updated');

            return $this->redirectToRoute('app_contact_show', [
                'slug' => $contact->getSlug(),
            ]);
        }

        return $this->renderForm('note/edit.html.twig', [
            'parent' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/contacts/csv', name: 'app_contact_csv', methods: ['GET'])]
    #[IsGranted('WORKSPACE_VIEW', subject: 'workspace')]
    public function outputCsv(Workspace $workspace): Response
    {
        $contacts = $this->contactRepository->findBy(['workspace' => $workspace]);

        $csvData = $this->csvService->getCsvContacts($contacts);

        $response = new Response($csvData);

        return $this->csvService->returnCsvResponse($response, 'contacts');
    }
}
