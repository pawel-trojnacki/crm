<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Dto\Transformer\UpdateUserInfoDtoTransformer;
use App\Dto\UpdatePasswordDto;
use App\Dto\UpdateUserInfoDto;
use App\Entity\User;
use App\Form\PasswordFormType;
use App\Form\UserInfoFormType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractBaseController
{
    public function __construct(
        private UserRepository $userRepository,
        private UpdateUserInfoDtoTransformer $updateUserInfoDtoTransformer,
    ) {
    }

    #[Route('/profile/{slug}', name: 'app_profile_show', methods: ['GET', 'POST'])]
    #[IsGranted('PROFILE_VIEW', subject: 'user')]
    public function show(User $user, Request $request): Response
    {
        $updateUserInfoDto = $this->updateUserInfoDtoTransformer->transformFromObject($user);

        $userForm = $this->createForm(UserInfoFormType::class, $updateUserInfoDto);

        $passwordForm = $this->createForm(PasswordFormType::class);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $this->denyAccessUnlessGranted('PROFILE_EDIT', $user);
            /** @var UpdateUserInfoDto $dto */

            $dto = $userForm->getData();

            $user->updateFromDto($dto);

            $this->userRepository->save($user);

            $this->addFlashSuccess('You account has been updated');

            return $this->redirectToRoute('app_profile_show', [
                'slug' => $user->getSlug(),
            ]);
        }

        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $this->denyAccessUnlessGranted('PROFILE_EDIT', $user);

            /** @var UpdatePasswordDto $passwordDto  */
            $passwordDto = $passwordForm->getData();

            $this->userRepository->register($user, $passwordDto->plainPassword);

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
