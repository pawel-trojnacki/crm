<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Workspace;
use App\Repository\ContactRepository;
use App\Service\ContactManager;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
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
            'pager' => $pager
        ]);
    }
}
