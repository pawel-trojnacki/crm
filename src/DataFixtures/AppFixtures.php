<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\CompanyFactory;
use App\Factory\ContactFactory;
use App\Factory\ContactNoteFactory;
use App\Factory\DealFactory;
use App\Factory\DealNoteFactory;
use App\Factory\IndustryFactory;
use App\Factory\MeetingFactory;
use App\Factory\UserFactory;
use App\Factory\WorkspaceFactory;
use App\Repository\CountryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function Zenstruck\Foundry\faker;

class AppFixtures extends Fixture
{
    public function __construct(
        private CountryRepository $countryRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private string $testAdminPassword,
        private string $testUserPassword,
    ) {
    }

    private function hashUserPassword(string $plainPassword): string
    {
        $user = new User();
        return $this->passwordHasher->hashPassword($user, $plainPassword);
    }

    public function load(ObjectManager $manager): void
    {
        /** @var EntityManagerInterface $manager */
        $manager->getConnection()->executeQuery(
            file_get_contents(__DIR__ . '/../../sql/countries.sql')
        );

        $manager->getConnection()->executeQuery(
            file_get_contents(__DIR__ . '/../../sql/industries.sql')
        );

        $manager->flush();

        $workspaceDate = new \DateTime();
        $workspaceDate->modify('-2 years');

        WorkspaceFactory::createOne([
            'name' => 'First Workspace',
            'createdAt' => $workspaceDate,
            'updatedAt' => faker()->dateTimeBetween('-2 years', 'now'),
        ]);

        UserFactory::createOne([
            'email' => 'testadmin@email.com',
            'password' => $this->hashUserPassword($this->testAdminPassword),
            'workspace' => WorkspaceFactory::random(),
            'roles' => ['ROLE_ADMIN'],
            'createdAt' => $workspaceDate,
            'updatedAt' => faker()->dateTimeBetween('-2 years', 'now'),
        ]);

        UserFactory::createOne([
            'email' => 'testuser@email.com',
            'password' => $this->hashUserPassword($this->testUserPassword),
            'workspace' => WorkspaceFactory::random(),
            'roles' => ['ROLE_USER'],
            'createdAt' => faker()->dateTimeBetween('-2 years', '-1 year'),
            'updatedAt' => faker()->dateTimeBetween('-1 year', 'now'),
        ]);

        UserFactory::createMany(3, [
            'workspace' => WorkspaceFactory::random(),
            'roles' => faker()->boolean() ? ['ROLE_USER'] : ['ROLE_MANAGER'],
            'createdAt' => faker()->dateTimeBetween('-2 years', '-1 year'),
            'updatedAt' => faker()->dateTimeBetween('-1 year', 'now'),
        ]);

        $countires = [
            $this->countryRepository->findOneBy(['isoCode' => 'US']),
            $this->countryRepository->findOneBy(['isoCode' => 'CA']),
            $this->countryRepository->findOneBy(['isoCode' => 'GB']),
        ];

        CompanyFactory::createMany(40, fn () => [
            'workspace' => WorkspaceFactory::random(),
            'industry' => IndustryFactory::random(),
            'creator' => UserFactory::random(),
            'country' => faker()->randomElement($countires),
        ]);

        ContactFactory::createMany(60, fn () => [
            'workspace' => WorkspaceFactory::random(),
            'creator' => UserFactory::random(),
            'company' => faker()->boolean(80) ? CompanyFactory::random() : null,
            'createdAt' => faker()->dateTimeBetween('-1 year', '-2 weeks'),
            'updatedAt' => faker()->dateTimeBetween('-2 weeks', 'now'),
        ]);

        ContactNoteFactory::createMany(200, fn () => [
            'parent' => ContactFactory::random(),
            'creator' => UserFactory::random(),
            'createdAt' => faker()->dateTimeBetween('-2 weeks', 'now'),
            'updatedAt' => faker()->dateTimeBetween('-1 week', 'now'),
        ]);

        DealFactory::createMany(35, fn () => [
            'workspace' => WorkspaceFactory::random(),
            'company' => CompanyFactory::random(),
            'creator' => UserFactory::random(),
            'users' => UserFactory::randomRange(1, 3),
            'createdAt' => faker()->dateTimeBetween('-1 year', '-2 weeks'),
            'updatedAt' => faker()->dateTimeBetween('-2 weeks', 'now'),
        ]);

        DealNoteFactory::createMany(120, fn () => [
            'parent' => DealFactory::random(),
            'creator' => UserFactory::random(),
            'createdAt' => faker()->dateTimeBetween('-2 weeks', 'now'),
            'updatedAt' => faker()->dateTimeBetween('-1 week', 'now'),
        ]);

        MeetingFactory::createMany(15, fn () => [
            'workspace' => WorkspaceFactory::random(),
        ]);
    }
}
