<?php

namespace App\Service;

use App\Entity\Workspace;
use App\Repository\ContactNoteRepository;
use App\Repository\DealNoteRepository;

class NoteService
{
    public function __construct(
        private ContactNoteRepository $contactNoteRepository,
        private DealNoteRepository $dealNoteRepository,
    ) {
    }

    public function findLatestNotesByWorkspace(Workspace $workspace, ?int $limit = 10)
    {
        $contactNotes = $this->contactNoteRepository->findLatestByWorkspace($workspace, $limit);
        $dealNotes = $this->dealNoteRepository->findLatestByWorkspace($workspace, $limit);

        $combinedNotes = [...$contactNotes, ...$dealNotes];

        usort($combinedNotes, function ($a, $b) {
            return $a->getCreatedAt() < $b->getCreatedAt();
        });

        return array_slice($combinedNotes, 0, $limit);
    }
}
