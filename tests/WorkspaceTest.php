<?php

namespace App\Tests;

use App\Entity\Workspace;
use App\Repository\WorkspaceRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WorkspaceTest extends KernelTestCase
{
    private WorkspaceRepository $workspaceRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);

        $this->workspaceRepository = self::getContainer()->get(WorkspaceRepository::class);
    }

    public function testWorkspaceIsCreatedInDatabase(): void
    {
        $name = 'Some Workspace';

        $workspace = new Workspace($name);

        $this->workspaceRepository->save($workspace);

        $savedWorkspace = $this->workspaceRepository->findOneBy(['name' => $name]);

        $this->assertInstanceOf(Workspace::class, $savedWorkspace);
        $this->assertIsString($savedWorkspace->getId());
        $this->assertIsString($savedWorkspace->getSlug());
        $this->assertInstanceOf('DateTime', $savedWorkspace->getCreatedAt());
        $this->assertInstanceOf('DateTime', $savedWorkspace->getUpdatedAt());
    }

    public function testWorkspaceIsDeletedFromDatabase(): void
    {
        $name = 'Some Workspace';

        $workspace = new Workspace($name);

        $this->workspaceRepository->save($workspace);

        $this->assertInstanceOf(
            Workspace::class,
            $this->workspaceRepository->findOneBy(['name' => $name])
        );

        $this->workspaceRepository->delete($workspace);

        $this->assertNull(
            $this->workspaceRepository->findOneBy(['name' => $name])
        );
    }
}
