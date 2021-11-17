<?php

namespace App\Tests;

use App\Entity\Contact;
use App\Entity\Workspace;
use App\Repository\ContactRepository;
use App\Repository\WorkspaceRepository;
use App\Tests\Helper\ContactTestHelper;
use App\Tests\Helper\WorkspaceTestHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContactTest extends KernelTestCase
{
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
            'email' => ContactTestHelper::DEFAULTS['email'],
        ]);

        $this->assertInstanceOf(Contact::class, $savedContact);
        $this->assertIsInt($savedContact->getId());
        $this->assertSame(
            ContactTestHelper::DEFAULTS['firstName'],
            $savedContact->getFirstName()
        );
        $this->assertSame(
            ContactTestHelper::DEFAULTS['lastName'],
            $savedContact->getLastName()
        );
        $this->assertSame(
            ContactTestHelper::DEFAULTS['phone'],
            $savedContact->getPhone()
        );
        $this->assertSame(
            ContactTestHelper::DEFAULTS['position'],
            $savedContact->getPosition()
        );
        $this->assertSame('john-doe', $savedContact->getSlug());
        $this->assertInstanceOf('DateTime', $savedContact->getCreatedAt());
        $this->assertInstanceOf('DateTime', $savedContact->getUpdatedAt());
        $this->assertNull($savedContact->getCompany());
    }

    public function testContactCanBeDeletedFromDatabase(): void
    {
        $workspace = WorkspaceTestHelper::createDefaultWorkspace();

        $this->workspaceRepository->save($workspace);

        $contact = $this->saveDefaultContact($workspace);

        $this->assertInstanceOf(
            Contact::class,
            $this->contactRepository->findOneBy(
                ['email' => ContactTestHelper::DEFAULTS['email']]
            )
        );

        $this->contactRepository->delete($contact);

        $this->assertNull(
            $this->contactRepository->findOneBy([
                'email' => ContactTestHelper::DEFAULTS['email']
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
                ['email' => ContactTestHelper::DEFAULTS['email']]
            )
        );

        $savedWorkspace = $this->workspaceRepository->findOneBy([
            'name' => 'Some Workspace',
        ]);

        $this->workspaceRepository->delete($savedWorkspace);

        $this->assertNull(
            $this->contactRepository->findOneBy([
                'email' => ContactTestHelper::DEFAULTS['email']
            ])
        );
    }

    private function saveDefaultContact(Workspace $workspace): Contact
    {
        $contact = ContactTestHelper::createDefaultContact($workspace);

        $this->contactRepository->save($contact);

        return $contact;
    }
}
