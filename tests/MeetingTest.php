<?php

namespace App\Tests;

use App\Entity\Contact;
use App\Entity\Meeting;
use App\Entity\User;
use App\Repository\MeetingRepository;
use App\Repository\UserRepository;
use App\Repository\WorkspaceRepository;
use App\Tests\Helper\ContactTestHelper;
use App\Tests\Helper\UserTestHelper;
use App\Tests\Helper\WorkspaceTestHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MeetingTest extends KernelTestCase
{
    private MeetingRepository $meetingRepository;
    private UserRepository $userRepository;
    private WorkspaceRepository $workspaceRepository;

    public function setUp(): void
    {
        $kernel = self::bootKernel();

        $container = self::getContainer();

        DatabasePrimer::prime($kernel);

        $this->meetingRepository = $container->get(MeetingRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->workspaceRepository = $container->get(WorkspaceRepository::class);
    }

    private function createDefaultMeeting(): void
    {
        $workspace = WorkspaceTestHelper::createDefaultWorkspace();
        $this->workspaceRepository->save($workspace);

        $user = UserTestHelper::createDefaultUser($workspace);
        $this->userRepository->register($user, 'plain password');

        $contact = ContactTestHelper::createDefaultContact($workspace, $user);

        $beginAt = new \DateTime();
        $beginAt->modify('+24 hours');

        $endAt = new \DateTime();
        $endAt->modify('+26 hours');

        $meeting = new Meeting($workspace, $user, 'Some Meeting', 1, $beginAt, $endAt, $contact);

        $this->meetingRepository->save($meeting);
    }

    public function testMeetingIsCorrectlyCreatedInDatabase(): void
    {
        $this->createDefaultMeeting();

        $meeting = $this->meetingRepository->findOneBy(['name' => 'Some Meeting']);

        $this->assertInstanceOf(Meeting::class, $meeting);
        $this->assertIsString($meeting->getId());
        $this->assertIsString($meeting->getSlug());
        $this->assertSame(1, $meeting->getImportance());
        $this->assertInstanceOf(User::class, $meeting->getCreator());
        $this->assertInstanceOf(Contact::class, $meeting->getContact());
        $this->assertInstanceOf(\DateTime::class, $meeting->getBeginAt());
        $this->assertInstanceOf(\DateTime::class, $meeting->getEndAt());

        $dateDiff = date_diff($meeting->getEndAt(), $meeting->getBeginAt());

        $this->assertSame(2, $dateDiff->h);
    }

    public function testMeetingIsCorrectlyRemovedFromDatabase(): void
    {
        $this->createDefaultMeeting();

        $meeting = $this->meetingRepository->findOneBy(['name' => 'Some Meeting']);

        $this->assertInstanceOf(Meeting::class, $meeting);

        $meetingId = $meeting->getId();

        $this->meetingRepository->delete($meeting);

        $this->assertNull(
            $this->meetingRepository->findOneBy(['id' => $meetingId])
        );
    }
}
