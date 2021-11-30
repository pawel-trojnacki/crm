<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Entity\User;
use App\Repository\DealRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractBaseController
{
    public function __construct(
        private UserRepository $userRepository,
        private DealRepository $dealRepository,
    ) {
    }

    #[Route('/profile/{slug}', name: 'app_profile_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted('WORKSPACE_VIEW', $user->getWorkspace());

        $assignedDeals = $this->dealRepository->findLatestByAssignedUser($user);

        return $this->render('profile/show.html.twig', [
            'assigned_deals' => $assignedDeals,
            'user' => $user,
            'is_user_page' => $user->getId() === $this->getUser()->getId(),
        ]);
    }
}
