<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\MeetingRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private const COLOR_LOW = '#0fcce3';
    private const COLOR_NORMAL = '#ef6e4b';
    private const COLOR_HIGH = '#f82362';

    private static $color = [self::COLOR_LOW, self::COLOR_NORMAL, self::COLOR_HIGH];

    public function __construct(
        private MeetingRepository $meetingRepository,
        private UrlGeneratorInterface $router,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar): void
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return;
        }

        if (!$token->isAuthenticated()) {
            return;
        }

        /** @var User $user */
        $user = $token->getUser();

        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        $meetings = $this->meetingRepository->findBy(['workspace' => $user->getWorkspace()]);

        foreach ($meetings as $meeting) {
            $meetingEvent = new Event(
                $meeting->getName(),
                $meeting->getBeginAt(),
                $meeting->getEndAt(),
            );

            $meetingEvent->setOptions([
                'backgroundColor' => self::$color[$meeting->getImportance()],
                'borderColor' => self::$color[$meeting->getImportance()],
            ]);

            $meetingEvent->addOption(
                'url',
                $this->router->generate('app_meeting_show', [
                    'slug' => $meeting->getSlug(),
                ]),
            );

            $calendar->addEvent($meetingEvent);
        }
    }
}
