<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Dto\RegisterUserDto;
use App\Dto\Transformer\UpdateUserInfoDtoTransformer;
use App\Dto\UpdatePasswordDto;
use App\Dto\UpdateUserInfoDto;
use App\Entity\User;
use App\Entity\Workspace;
use App\Form\MemberFormType;
use App\Form\PasswordFormType;
use App\Form\UserInfoFormType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TeamController extends AbstractBaseController
{
    public function __construct(
        private UserRepository $userRepository,
        private UpdateUserInfoDtoTransformer $updateUserInfoDtoTransformer,
    ) {
    }

    #[Route('/{slug}/team', name: 'app_team_index', methods: ['GET', 'POST'])]
    #[IsGranted('WORKSPACE_VIEW', subject: 'workspace')]
    public function index(Workspace $workspace, Request $request): Response
    {
        if ($request->isMethod('POST') && $request->request->get('delete-member')) {
            $this->denyAccessUnlessGranted(
                'WORKSPACE_EDIT',
                $workspace,
                'Current user is not authorized to delete this user',
            );

            $userId = $request->request->get('delete-id');

            $user = $this->userRepository->findOneBy(['id' => $userId]);

            $this->userRepository->delete($user);

            $this->addFlashSuccess('User has been deleted');

            return $this->redirectToRoute('app_team_index', [
                'slug' => $workspace->getSlug(),
            ]);
        }
        return $this->render('team/index.html.twig');
    }

    #[Route('/{slug}/team/create-user', name: 'app_team_create', methods: ['GET', 'POST'])]
    #[IsGranted('WORKSPACE_EDIT', subject: 'workspace')]
    public function create(Workspace $workspace, Request $request): Response
    {
        $form = $this->createForm(MemberFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RegisterUserDto $dto */
            $dto = $form->getData();

            $user = User::createFromRegisterDto($workspace, $dto);

            $plainPassword = $dto->plainPassword;

            $this->userRepository->register($user, $plainPassword);

            $this->addFlashSuccess('User has been created');

            return $this->redirectToRoute('app_team_index', [
                'slug' => $workspace->getSlug(),
            ]);
        }

        return $this->renderForm('team/create.html.twig', [
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

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            throw new AccessDeniedException('Cannot edit admin user');
        }

        $updateUserInfoDto = $this->updateUserInfoDtoTransformer->transformFromObject($user);

        $form = $this->createForm(UserInfoFormType::class, $updateUserInfoDto, [
            'with_roles' => true,
        ]);

        $passwordForm = $this->createForm(PasswordFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UpdateUserInfoDto $dto */
            $dto = $form->getData();

            $user->updateFromDto($dto);
            
            $this->userRepository->save($user);

            $this->addFlashSuccess('User info has been updated');

            return $this->redirectToRoute('app_team_index', [
                'slug' => $workspace->getSlug(),
            ]);
        }

        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            /** @var UpdatePasswordDto $passwordDto  */
            $passwordDto = $passwordForm->getData();

            $this->userRepository->register($user, $passwordDto->plainPassword);

            $this->addFlashSuccess('User password has been changed');

            return $this->redirectToRoute('app_team_index', [
                'slug' => $workspace->getSlug(),
            ]);
        }

        return $this->renderForm('team/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'password_form' => $passwordForm,
        ]);
    }
}
