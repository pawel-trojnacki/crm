<?php

namespace App\Tests\Helper;

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
