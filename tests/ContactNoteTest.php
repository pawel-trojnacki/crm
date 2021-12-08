<?php

namespace App\Tests;

use App\Entity\ContactNote;
use App\Repository\ContactNoteRepository;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use App\Repository\WorkspaceRepository;
use App\Tests\Helper\ContactTestHelper;
use App\Tests\Helper\UserTestHelper;
use App\Tests\Helper\WorkspaceTestHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContactNoteTest extends KernelTestCase
{
    private const DEFAULT_CONTENT = 'Lorem ispum dolor sit amet';

    private WorkspaceRepository $workspaceRepository;
    private ContactRepository $contactRepository;
    private ContactNoteRepository $contactNoteRepository;
    private UserRepository $userRepository;
    private EntityManager $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $container = self::getContainer();

        DatabasePrimer::prime($kernel);

        $this->workspaceRepository = $container->get(WorkspaceRepository::class);
        $this->contactRepository = $container->get(ContactRepository::class);
        $this->contactNoteRepository = $container->get(ContactNoteRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    private function saveDefaultNote(): void
    {
        $workspace = WorkspaceTestHelper::createDefaultWorkspace();
        $this->workspaceRepository->save($workspace);

        $user = UserTestHelper::createDefaultUser($workspace);
        $this->userRepository->register($user, 'some password');

        $contact = ContactTestHelper::createDefaultContact($workspace, $user);
        $this->contactRepository->save($contact);

        $contactNote = new ContactNote(
            $contact,
            $user,
            self::DEFAULT_CONTENT,
        );

        $this->contactNoteRepository->save($contactNote);

        $this->em->clear();
    }

    public function testContactNoteIsCorrectlyCreatedAndRemovedFromDatabase(): void
    {
        $this->saveDefaultNote();

        $savedContact = $this->contactRepository->findOneBy([
            'email' => ContactTestHelper::DEFAULTS['email']
        ]);

        $savedNote = $this->contactNoteRepository->findOneBy([
            'parent' => $savedContact,
        ]);

        $this->assertInstanceOf(ContactNote::class, $savedNote);
        $this->assertIsString($savedNote->getId());
        $this->assertSame(self::DEFAULT_CONTENT, $savedNote->getContent());
        $this->assertSame(
            UserTestHelper::DEFAULTS['email'],
            $savedNote->getCreator()->getEmail()
        );

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
                'parent' => $savedContact,
            ])
        );

        $this->contactRepository->delete($savedContact);

        $this->assertNull(
            $this->contactNoteRepository->findOneBy([
                'parent' => $savedContact,
            ])
        );
    }
}
