<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Service\UserManager;
use App\Service\WorkspaceManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractBaseController
{
    public function __construct(
        private UserManager $userManager,
        private WorkspaceManager $workspaceManager,
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

            $workspace = $this->workspaceManager->createAndSave($workspaceName);

            /** @var User $user */
            $user = $form->getData();
            $plainPassword = $form->get('plainPassword')->getData();

            $this->userManager->register($user, $plainPassword, $workspace);

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
