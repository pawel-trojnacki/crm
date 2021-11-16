<?php

namespace App\DataFixtures;

use App\Factory\CompanyFactory;
use App\Factory\ContactFactory;
use App\Factory\IndustryFactory;
use App\Factory\WorkspaceFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (IndustryFactory::industries as $industry) {
            IndustryFactory::createOne([
                'name' => $industry
            ]);
        }

        WorkspaceFactory::createOne([
            'name' => 'First Workspace',
        ]);

        CompanyFactory::createMany(5, fn () => [
            'workspace' => WorkspaceFactory::random(),
            'industry' => IndustryFactory::random(),
        ]);

        ContactFactory::createMany(50, [
            'workspace' => WorkspaceFactory::random(),
        ]);
    }
}
