<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractBaseController;
use App\Entity\Meeting;
use App\Entity\Workspace;
use App\Form\MeetingFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeetingController extends AbstractBaseController
{
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
        $form = $this->createForm(MeetingFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
        }

        return $this->renderForm('meeting/create.html.twig', [
            'form' => $form,
        ]);
    }
}
