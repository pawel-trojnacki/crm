<?php

namespace App\Controller\Abstract;

use App\Entity\Abstract\AbstractNoteEntity;
use App\Entity\Interface\NoteParentEntityInterface;
use App\Entity\Workspace;
use App\Form\NoteFormType;
use App\Repository\Interface\NoteRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractNoteController extends AbstractBaseController
{
    protected function saveNote(
        NoteRepositoryInterface $noteRepository,
        Workspace $workspace,
        FormInterface $form,
        NoteParentEntityInterface $parent
    ): void {
        $this->denyAccessUnlessGranted(
            'WORKSPACE_ADD_ITEM',
            $workspace,
            'Current user is not authorized to create a note'
        );

        /** @var AbstractNoteEntity $note */
        $note = $form->getData();

        $note->setParent($parent);
        $note->setCreator($this->getUser());

        $noteRepository->save($note);

        $this->addFlashSuccess('Note has been created');
    }

    protected function deleteNote(NoteRepositoryInterface $noteRepository, Request $request): void
    {
        $noteId = $request->request->get('delete-id');

        $note = $noteRepository->findOneBy(['id' => $noteId]);

        $this->denyAccessUnlessGranted(
            'NOTE_EDIT',
            $note,
            'Current user is not authorized to delete this note',
        );

        $noteRepository->delete($note);

        $this->addFlashSuccess('Note has been deleted');
    }

    protected function editNote(
        int $noteId,
        string $slug,
        Request $request,
        NoteRepositoryInterface $noteRepository,
        ServiceEntityRepository $parentRepository,
        string $dataClass,
        string $redirectPath,
    ): Response {
        $note = $noteRepository->findOneBy(['id' => $noteId]);

        /** @var NoteParentEntityInterface $parent */
        $parent = $parentRepository->findOneBy(['slug' => $slug]);
        $workspace = $parent->getWorkspace();

        $this->denyAccessUnlessGranted(
            'NOTE_EDIT',
            $note,
            'Current user is not authorized to edit this note'
        );

        $form = $this->createForm(NoteFormType::class, $note, [
            'data_class' => $dataClass,
            'label_text' => 'Edit note',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $note = $form->getData();

            $noteRepository->save($note);

            $this->addFlashSuccess('Note has been updated');

            return $this->redirectToRoute($redirectPath, [
                'slug' => $parent->getSlug(),
            ]);
        }

        return $this->renderForm('note/edit.html.twig', [
            'parent' => $parent,
            'form' => $form,
        ]);
    }
}
