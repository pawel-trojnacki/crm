<?php

namespace App\Tests;

use App\Entity\Contact;
use App\Entity\Workspace;
use App\Repository\ContactRepository;
use App\Repository\WorkspaceRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContactTest extends KernelTestCase
{
    private const DEFAULTS = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'johndoe@email.com',
        'phone' => '541-814-1739',
        'position' => 'manager',
    ];

    private ContactRepository $contactRepository;
    private WorkspaceRepository $workspaceRepository;
    private EntityManager $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $container = self::getContainer();

        DatabasePrimer::prime($kernel);

        $this->contactRepository = $container->get(ContactRepository::class);
        $this->workspaceRepository = $container->get(WorkspaceRepository::class);
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    public function testContactIsCreatedInDatabase(): void
    {
        $workspace = WorkspaceTestHelper::createDefaultWorkspace();

        $this->workspaceRepository->save($workspace);

        $this->saveDefaultContact($workspace);

        $savedContact = $this->contactRepository->findOneBy([
            'email' => self::DEFAULTS['email'],
        ]);

        $this->assertInstanceOf(Contact::class, $savedContact);
        $this->assertIsInt($savedContact->getId());
        $this->assertSame(self::DEFAULTS['firstName'], $savedContact->getFirstName());
        $this->assertSame(self::DEFAULTS['lastName'], $savedContact->getLastName());
        $this->assertSame(self::DEFAULTS['phone'], $savedContact->getPhone());
        $this->assertSame(self::DEFAULTS['position'], $savedContact->getPosition());
        $this->assertSame('john-doe', $savedContact->getSlug());
        $this->assertInstanceOf('DateTime', $savedContact->getCreatedAt());
        $this->assertInstanceOf('DateTime', $savedContact->getUpdatedAt());
    }

    public function testContactCanBeDeletedFromDatabase(): void
    {
        $workspace = WorkspaceTestHelper::createDefaultWorkspace();

        $this->workspaceRepository->save($workspace);

        $contact = $this->saveDefaultContact($workspace);

        $this->assertInstanceOf(
            Contact::class,
            $this->contactRepository->findOneBy(
                ['email' => self::DEFAULTS['email']]
            )
        );

        $this->contactRepository->delete($contact);

        $this->assertNull(
            $this->contactRepository->findOneBy([
                'email' => self::DEFAULTS['email']
            ])
        );
    }

    public function testContactIsDeletedWhenWorkspaceIsDeleted(): void
    {
        $workspace = WorkspaceTestHelper::createDefaultWorkspace();

        $this->workspaceRepository->save($workspace);

        $this->saveDefaultContact($workspace);

        $this->em->clear();

        $this->assertInstanceOf(
            Contact::class,
            $this->contactRepository->findOneBy(
                ['email' => self::DEFAULTS['email']]
            )
        );

        $savedWorkspace = $this->workspaceRepository->findOneBy([
            'name' => 'Some Workspace',
        ]);

        $this->workspaceRepository->delete($savedWorkspace);

        $this->assertNull(
            $this->contactRepository->findOneBy([
                'email' => self::DEFAULTS['email']
            ])
        );
    }

    private function createDefaultContact(Workspace $workspace): Contact
    {
        $contact = new Contact();

        $contact->setFirstName(self::DEFAULTS['firstName']);
        $contact->setLastName(self::DEFAULTS['lastName']);
        $contact->setEmail(self::DEFAULTS['email']);
        $contact->setPhone(self::DEFAULTS['phone']);
        $contact->setPosition(self::DEFAULTS['position']);
        $contact->setWorkspace($workspace);

        return $contact;
    }

    private function saveDefaultContact(Workspace $workspace): Contact
    {
        $contact = $this->createDefaultContact($workspace);

        $this->contactRepository->save($contact);

        return $contact;
    }
}
