<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\CompanyFactory;
use App\Factory\ContactFactory;
use App\Factory\ContactNoteFactory;
use App\Factory\DealFactory;
use App\Factory\DealNoteFactory;
use App\Factory\IndustryFactory;
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

        $manager->flush();

        foreach (IndustryFactory::INDUSTRIES as $industry) {
            IndustryFactory::createOne([
                'name' => $industry
            ]);
        }

        WorkspaceFactory::createOne([
            'name' => 'First Workspace',
        ]);

        UserFactory::createOne([
            'email' => 'test@email.com',
            'password' => $this->hashUserPassword('00000000'),
            'workspace' => WorkspaceFactory::random(),
            'roles' => ['ROLE_ADMIN'],
        ]);

        UserFactory::createMany(3, [
            'workspace' => WorkspaceFactory::random(),
        ]);

        CompanyFactory::createMany(40, fn () => [
            'workspace' => WorkspaceFactory::random(),
            'industry' => IndustryFactory::random(),
            'creator' => UserFactory::random(),
            'country' => $this->countryRepository->findOneBy(['isoCode' => 'US']),
            'createdAt' => faker()->dateTimeBetween('-1 year', '-2 weeks'),
            'updatedAt' => faker()->dateTimeBetween('-2 weeks', 'now'),
        ]);

        ContactFactory::createMany(60, fn () => [
            'workspace' => WorkspaceFactory::random(),
            'creator' => UserFactory::random(),
            'company' => faker()->boolean(80) ? CompanyFactory::random() : null,
            'createdAt' => faker()->dateTimeBetween('-1 year', '-2 weeks'),
            'updatedAt' => faker()->dateTimeBetween('-2 weeks', 'now'),
        ]);

        ContactNoteFactory::createMany(200, fn () => [
            'contact' => ContactFactory::random(),
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
            'deal' => DealFactory::random(),
            'creator' => UserFactory::random(),
            'createdAt' => faker()->dateTimeBetween('-2 weeks', 'now'),
            'updatedAt' => faker()->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
