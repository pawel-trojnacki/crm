<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Entity\User;
use App\Entity\Workspace;
use App\Form\PasswordFormType;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractBaseController
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    #[Route('/{slug}/team', name: 'app_team_index', methods: ['GET'])]
    #[IsGranted('WORKSPACE_VIEW', subject: 'workspace')]
    public function index(Workspace $workspace): Response
    {
        return $this->render('team/index.html.twig', [
            'workspace' => $workspace,
        ]);
    }

    #[Route('/{slug}/team/create-user', name: 'app_team_create', methods: ['GET', 'POST'])]
    #[IsGranted('WORKSPACE_EDIT', subject: 'workspace')]
    public function create(Workspace $workspace, Request $request): Response
    {
        $form = $this->createForm(UserFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setWorkspace($workspace);

            $userRole = $form->get('role')->getData();
            $user->addRole($userRole);

            $plainPassword = $form->get('plainPassword')->getData();

            $this->userRepository->register($user, $plainPassword);

            return $this->redirectToRoute('app_team_index', [
                'slug' => $workspace->getSlug(),
            ]);
        }

        return $this->renderForm('team/create.html.twig', [
            'workspace' => $workspace,
            'form' => $form,
        ]);
    }

    #[Route('/team/{slug}/edit', name: 'app_team_edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request): Response
    {
        $workspace = $user->getWorkspace();

        $this->denyAccessUnlessGranted(
            'WORKSPACE_EDIT',
            $workspace,
            'Current user is not allowed to edit this team member',
        );

        $form = $this->createForm(UserFormType::class, $user, [
            'with_password' => false,
        ]);

        $passwordForm = $this->createForm(PasswordFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $userRole = $form->get('role')->getData();

            $user->setRoles([$userRole]);

            $this->userRepository->save($user);

            return $this->redirectToRoute('app_team_index', [
                'slug' => $workspace->getSlug(),
            ]);
        }

        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $plainPassword = $passwordForm->get('plainPassword')->getData();
            $this->userRepository->register($user, $plainPassword);

            return $this->redirectToRoute('app_team_index', [
                'slug' => $workspace->getSlug(),
            ]);
        }

        return $this->renderForm('team/edit.html.twig', [
            'workspace' => $workspace,
            'user' => $user,
            'form' => $form,
            'passwordForm' => $passwordForm,
        ]);
    }
}
