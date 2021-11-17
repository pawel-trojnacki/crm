<?php

namespace App\Controller;

use App\Constant\ContactConstant;
use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Contact;
use App\Entity\Workspace;
use App\Form\ContactFormType;
use App\Service\ContactManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractBaseController
{
    public function __construct(
        private ContactManager $contactManager,
    ) {
    }

    #[Route('{slug}/contacts/', name: 'app_contact_index', methods: ['GET'])]
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
    public function show(Contact $contact, Request $request): Response
    {
        if ($request->isMethod('POST') && $request->request->get('delete')) {
            $this->contactManager->delete($contact);

            return $this->redirectToRoute('app_contact_index', [
                'slug' => $contact->getWorkspace()->getSlug()
            ]);
        }

        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
            'workspace' => $contact->getWorkspace(),
        ]);
    }

    #[Route('{slug}/contacts/create', name: 'app_contact_create', methods: ['GET', 'POST'])]
    public function create(Workspace $workspace, Request $request): Response
    {
        $form = $this->createForm(type: ContactFormType::class, options: [
            'workspace' => $workspace,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->contactManager->save($form, $workspace);

            $referer = $request->request->get('referer');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_contact_index', [
                    'slug' => $workspace->getSlug()
                ]);
            }
        }

        return $this->renderForm('contact/create.html.twig', [
            'form' => $form,
            'workspace' => $workspace,
        ]);
    }

    #[Route('contact/{slug}/edit', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Contact $contact, Request $request): Response
    {
        $workspace = $contact->getWorkspace();

        $form = $this->createForm(ContactFormType::class, $contact, [
            'workspace' => $workspace,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->contactManager->save($form, $workspace);

            $referer = $request->request->get('referer');
            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_contact_index', [
                    'slug' => $workspace->getSlug()
                ]);
            }
        }

        return $this->renderForm('contact/edit.html.twig', [
            'form' => $form,
            'workspace' => $workspace,
            'contact' => $contact,
        ]);
    }
}
