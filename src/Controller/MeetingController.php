<?php

namespace App\Controller;

use App\Constant\Design\MeetingColorConstant;
use App\Controller\Abstract\AbstractBaseController;
use App\Dto\MeetingDto;
use App\Dto\Transformer\MeetingDtoTransformer;
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
        private MeetingDtoTransformer $meetingDtoTransformer,
    ) {
    }

    #[Route('/{slug}/meetings', name: 'app_meeting_index', methods: ['GET'])]
    #[IsGranted('WORKSPACE_VIEW', subject: 'workspace')]
    public function index(Workspace $workspace): Response
    {
        return $this->render('meeting/calendar.html.twig', [
            'importance_options' => Meeting::getImportanceOptions(),
            'importance_colors' => MeetingColorConstant::COLORS,
        ]);
    }

    #[Route('/meeting/{slug}', name: 'app_meeting_show', methods: ['GET', 'POST'])]
    #[IsGranted('MEETING_VIEW', subject: 'meeting')]
    public function show(Meeting $meeting, Request $request): Response
    {
        $workspace = $meeting->getWorkspace();

        $meetingDto = $this->meetingDtoTransformer->transformFromObject($meeting);

        $form = $this->createForm(MeetingFormType::class, $meetingDto, [
            'workspace' => $workspace,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('MEETING_EDIT', $meeting);

            /** @var MeetingDto $dto */
            $dto = $form->getData();

            $meeting->updateFromDto($dto);

            $this->meetingRepository->save($meeting);

            $this->addFlashSuccess(sprintf('Meeting %s has been updated', $meeting->getName()));

            return $this->redirectToRoute('app_meeting_show', [
                'slug' => $meeting->getSlug(),
            ]);
        }

        if ($request->isMethod('POST') && $request->request->get('delete-meeting')) {
            $this->denyAccessUnlessGranted('MEETING_EDIT', $meeting);

            $meetingName = $meeting->getName();

            $this->meetingRepository->delete($meeting);

            $this->addFlashSuccess(sprintf(
                'Meeting %s has been deleted',
                $meetingName,
            ));

            return $this->redirectToRoute('app_meeting_index', [
                'slug' => $workspace->getSlug(),
            ]);
        }

        return $this->renderForm('meeting/show.html.twig', [
            'form' => $form,
            'meeting' => $meeting,
        ]);
    }

    #[Route('/{slug}/meetings/create', name: 'app_meeting_create', methods: ['GET', 'POST'])]
    #[IsGranted('WORKSPACE_ADD_ITEM', subject: 'workspace')]
    public function create(Workspace $workspace, Request $request): Response
    {
        $form = $this->createForm(MeetingFormType::class, null, [
            'workspace' => $workspace,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MeetingDto $dto */
            $dto = $form->getData();

            $meeting = Meeting::createFromDto($workspace, $this->getUser(), $dto);

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
