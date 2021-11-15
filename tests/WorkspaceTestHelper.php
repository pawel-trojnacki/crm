<?php

namespace App\Tests;

use App\Entity\Workspace;

class WorkspaceTestHelper
{
    public static function createDefaultWorkspace(): Workspace
    {
        $workspace = new Workspace();
        $workspace->setName('Some Workspace');

        return $workspace;
    }
}
