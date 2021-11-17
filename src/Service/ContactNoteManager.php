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
}
