<?php

namespace App\DataFixtures;

use App\Factory\CompanyFactory;
use App\Factory\ContactFactory;
use App\Factory\IndustryFactory;
use App\Factory\WorkspaceFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use function Zenstruck\Foundry\faker;

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

        ContactFactory::createMany(50, fn () => [
            'workspace' => WorkspaceFactory::random(),
            'company' => faker()->boolean(70) ? CompanyFactory::random() : null,
        ]);
    }
}
