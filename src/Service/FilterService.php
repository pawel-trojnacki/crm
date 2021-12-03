<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Workspace;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FilterService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function getCurrentPage(Request $request, ?string $param = 'page'): int
    {
        $currentPage = $request->query->get($param, 1);

        if (!is_numeric($currentPage)) {
            throw new NotFoundHttpException('Page not found');
        }

        return $currentPage;
    }

    public function getUserIdBySlugParam(Request $request, ?string $param = 'user'): ?int
    {
        $userSlug = $request->query->get($param);

        $selectedUser = $this->userRepository->findOneBy(['slug' => $userSlug]);

        return $selectedUser ? $selectedUser->getId() : null;
    }

    /** @return User[] */
    public function findTeamMembersByWorkspace(Workspace $workspace): array
    {
        return $this->userRepository->findAllByWorkspaceAlphabetically($workspace);
    }

    public function getSearch(Request $request): ?string
    {
        return $request->query->get('search');
    }

    public function getOrder(Request $request): ?string
    {
        return $request->query->get('order');
    }

    public function getIndustry(Request $request): ?string
    {
        return $request->query->get('industry');
    }

    public function getStageId(Request $request): ?string
    {
        return $request->query->get('stage_id');
    }
}
