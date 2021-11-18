<?php

namespace App\Service;

use App\Entity\Contact;
use App\Entity\ContactNote;
use App\Repository\ContactNoteRepository;
use Symfony\Component\Form\FormInterface;

class ContactNoteManager
{
    public function __construct(
        private ContactNoteRepository $contactNoteRepository,
    ) {
    }

    public function save(FormInterface $form, Contact $contact): void
    {
        /** @var ContactNote $contactNote */
        $contactNote = $form->getData();

        $contactNote->setContact($contact);

        $this->contactNoteRepository->save($contactNote);
    }

    public function deleteById(int $id): void
    {
        $contactNote = $this->findOneById($id);

        if ($contactNote) {
            $this->contactNoteRepository->delete($contactNote);
        }
    }

    public function findOneById(int $id): ?ContactNote
    {
        return $this->contactNoteRepository->findOneBy(['id' => $id]);
    }
}
