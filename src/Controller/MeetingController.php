<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Dto\MeetingDto;
use App\Entity\Meeting;
use App\Entity\Workspace;
use App\Form\MeetingFormType;
use App\Repository\MeetingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeetingController extends AbstractBaseController
{
    public function __construct(
        private MeetingRepository $meetingRepository,
    ) {
    }

    #[Route('/{slug}/meetings', name: 'app_meeting_index', methods: ['GET'])]
    #[IsGranted('WORKSPACE_VIEW', subject: 'workspace')]
    public function index(Workspace $workspace): Response
    {
        return $this->render('meeting/calendar.html.twig');
    }

    #[Route('/meeting/{slug}', name: 'app_meeting_show', methods: ['GET'])]
    public function show(Meeting $meeting): Response
    {
        return $this->render('meeting/show.html.twig');
    }

    #[Route('/{slug}/meetings/create', name: 'app_meeting_create', methods: ['GET', 'POST'])]
    public function create(Workspace $workspace, Request $request): Response
    {
        $form = $this->createForm(MeetingFormType::class, null, [
            'workspace' => $workspace,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MeetingDto $dto */
            $dto = $form->getData();

            $meeting = Meeting::createFromDto($workspace, $dto);

            $this->meetingRepository->save($meeting);

            $this->addFlashSuccess('Meeting has been created');

            return $this->redirectToRoute('app_meeting_show', [
                'slug' => $meeting->getSlug(),
            ]);
        }

        return $this->renderForm('meeting/create.html.twig', [
            'form' => $form,
        ]);
    }
}
