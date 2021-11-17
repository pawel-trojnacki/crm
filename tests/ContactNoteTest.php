<?php

namespace App\Tests;

use App\Entity\ContactNote;
use App\Repository\ContactNoteRepository;
use App\Repository\ContactRepository;
use App\Repository\WorkspaceRepository;
use App\Tests\Helper\ContactTestHelper;
use App\Tests\Helper\WorkspaceTestHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContactNoteTest extends KernelTestCase
{
    private const DEFAULT_CONTENT = 'Lorem ispum dolor sit amet';

    private WorkspaceRepository $workspaceRepository;
    private ContactRepository $contactRepository;
    private ContactNoteRepository $contactNoteRepository;
    private EntityManager $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $container = self::getContainer();

        DatabasePrimer::prime($kernel);

        $this->workspaceRepository = $container->get(WorkspaceRepository::class);
        $this->contactRepository = $container->get(ContactRepository::class);
        $this->contactNoteRepository = $container->get(ContactNoteRepository::class);
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    public function testContactNoteIsCorrectlyCreatedAndRemovedFromDatabase(): void
    {
        $this->saveDefaultNote();

        $savedContact = $this->contactRepository->findOneBy([
            'email' => ContactTestHelper::DEFAULTS['email']
        ]);

        $savedNote = $this->contactNoteRepository->findOneBy([
            'contact' => $savedContact,
        ]);

        $this->assertInstanceOf(ContactNote::class, $savedNote);
        $this->assertIsInt($savedNote->getId());
        $this->assertSame(self::DEFAULT_CONTENT, $savedNote->getContent());

        $this->contactNoteRepository->delete($savedNote);

        $this->assertNull(
            $this->contactNoteRepository->findOneBy([
                'id' => $savedNote->getId()
            ])
        );
    }

    public function testNoteIsDeletedWhenRelatedContactIsDeleted(): void
    {
        $this->saveDefaultNote();

        $savedContact = $this->contactRepository->findOneBy([
            'email' => ContactTestHelper::DEFAULTS['email']
        ]);

        $this->assertInstanceOf(
            ContactNote::class,
            $this->contactNoteRepository->findOneBy([
                'contact' => $savedContact,
            ])
        );

        $this->contactRepository->delete($savedContact);

        $this->assertNull(
            $this->contactNoteRepository->findOneBy([
                'contact' => $savedContact,
            ])
        );
    }

    private function saveDefaultNote(): void
    {
        $workspace = WorkspaceTestHelper::createDefaultWorkspace();
        $this->workspaceRepository->save($workspace);

        $contact = ContactTestHelper::createDefaultContact($workspace);
        $this->contactRepository->save($contact);

        $contactNote = new ContactNote();
        $contactNote->setContent(self::DEFAULT_CONTENT);
        $contactNote->setContact($contact);

        $this->contactNoteRepository->save($contactNote);

        $this->em->clear();
    }
}
