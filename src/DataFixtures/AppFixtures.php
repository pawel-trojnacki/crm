<?php

namespace App\DataFixtures;

use App\Factory\WorkspaceFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        WorkspaceFactory::createOne([
            'name' => 'First Workspace',
        ]);
    }
}
