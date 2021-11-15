<?php

namespace App\Service;

use App\Entity\Workspace;
use App\Repository\WorkspaceRepository;

class WorkspaceManager
{
    public function __construct(
        private WorkspaceRepository $workspaceRepository,
    ) {
    }

    public function save(Workspace $workspace): void
    {
        $this->workspaceRepository->save($workspace);
    }
}
