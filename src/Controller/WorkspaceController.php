<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Workspace;
use App\Repository\WorkspaceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WorkspaceController extends AbstractBaseController
{
    public function __construct(
        private WorkspaceRepository $workspaceRepository,
        private ValidatorInterface $validator,
        private TokenStorageInterface $tokenStorage,
        private SessionInterface $session,
    ) {
    }

    #[Route('/{slug}/manage-workspace', name: 'app_workspace_edit', methods: ['GET', 'POST'])]
    #[IsGranted('WORKSPACE_EDIT', subject: 'workspace')]
    public function edit(Workspace $workspace, Request $request): Response
    {
        $errors = [];
        $editedName = $request->request->get('edit-workspace-name');

        if ($request->isMethod('POST') && $editedName) {
            $workspace->changeName($editedName);

            $errors = $this->validator->validate($workspace);

            if (count($errors) === 0) {
                $this->workspaceRepository->save($workspace);

                return $this->redirectToRoute('app_workspace_edit', [
                    'slug' => $workspace->getSlug(),
                ]);
            }
        }

        if ($request->isMethod('POST') && $request->request->get('delete-workspace')) {
            $this->workspaceRepository->delete($workspace);

            $this->tokenStorage->setToken(null);
            $this->session->invalidate();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('workspace/edit.html.twig', [
            'errors' => $errors,
            'edited_name' => $editedName ?? $workspace->getName(),
        ]);
    }
}
