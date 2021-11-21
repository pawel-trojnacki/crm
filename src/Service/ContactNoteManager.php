<?php

namespace App\Service;

use App\Entity\Contact;
use App\Entity\ContactNote;
use App\Entity\User;
use App\Repository\ContactNoteRepository;

class ContactNoteManager
{
    public function __construct(
        private ContactNoteRepository $contactNoteRepository,
    ) {
    }

    public function save(ContactNote $contactNote, Contact $contact, User $user): void
    {
        $contactNote->setContact($contact);
        $contactNote->setCreator($user);

        $this->contactNoteRepository->save($contactNote);
    }

    public function update(ContactNote $contactNote): void
    {
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
