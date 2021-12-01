<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Entity\User;
use App\Form\PasswordFormType;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractBaseController
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    #[Route('/profile/{slug}', name: 'app_profile_show', methods: ['GET', 'POST'])]
    #[IsGranted('PROFILE_VIEW', subject: 'user')]
    public function show(User $user, Request $request): Response
    {
        $userForm = $this->createForm(UserFormType::class, $user, [
            'with_user_role' => false,
            'with_password' => false,
        ]);

        $passwordForm = $this->createForm(PasswordFormType::class);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $this->denyAccessUnlessGranted('PROFILE_EDIT', $user);

            /** @var User $editedUser */
            $editedUser = $userForm->getData();

            $this->userRepository->save($editedUser);

            $this->addFlashSuccess('You account has been updated');

            return $this->redirectToRoute('app_profile_show', [
                'slug' => $editedUser->getSlug(),
            ]);
        }

        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $this->denyAccessUnlessGranted('PROFILE_EDIT', $user);

            $plainPassword = $passwordForm->get('plainPassword')->getData();
            $this->userRepository->register($user, $plainPassword);

            $this->addFlashSuccess('Your password has been changed');

            return $this->redirectToRoute('app_profile_show', [
                'slug' => $user->getSlug(),
            ]);
        }

        return $this->renderForm('profile/show.html.twig', [
            'user_form' => $userForm,
            'password_form' => $passwordForm,
        ]);
    }
}
