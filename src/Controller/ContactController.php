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
use Symfony\Component\HttpFoundation\Session\Session;
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

        $session = new Session();
        $session->set('contact_index_params', $request->query->all());

        $pager = $this->contactManager->createPager($workspace, $currentPage, $order, $search);

        return $this->render('/contact/index.html.twig', [
            'workspace' => $workspace,
            'pager' => $pager,
            'order' => $order,
            'search' => $search,
            'sortOptions' => ContactConstant::SORT_OPTIONS,
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

            $session = new Session();
            $params = $session->get('contact_index_params');
            $params['slug'] = $workspace->getSlug();

            return $this->redirectToRoute('app_contact_index', $params);
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

            $session = new Session();
            $params = $session->get('contact_index_params');
            $params['slug'] = $workspace->getSlug();

            return $this->redirectToRoute('app_contact_index', $params);
        }

        return $this->renderForm('contact/edit.html.twig', [
            'form' => $form,
            'workspace' => $workspace,
            'contact' => $contact,
        ]);
    }
}
