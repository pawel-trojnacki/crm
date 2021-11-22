<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Entity\User;
use App\Entity\Workspace;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Repository\WorkspaceRepository;
use App\Security\AppAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractBaseController
{
    public function __construct(
        private UserRepository $userRepository,
        private WorkspaceRepository $workspaceRepository,
        private UserAuthenticatorInterface $userAuthenticator,
        private AppAuthenticator $appAuthenticator,
    ) {
    }
    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $workspaceName */
            $workspaceName = $form->get('workspace')->getData();

            $workspace = new Workspace();
            $workspace->setName($workspaceName);

            $this->workspaceRepository->save($workspace);

            /** @var User $user */
            $user = $form->getData();
            $plainPassword = $form->get('plainPassword')->getData();

            $user->addRole('ROLE_ADMIN');
            $user->setWorkspace($workspace);

            $this->userRepository->register($user, $plainPassword);

            return $this->userAuthenticator->authenticateUser(
                $user,
                $this->appAuthenticator,
                $request
            );
        }

        return $this->renderForm('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
