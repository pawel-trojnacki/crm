<?php

namespace App\Repository\Interface;

use App\Entity\Abstract\AbstractNoteEntity;

interface NoteRepositoryInterface
{
    public function save(AbstractNoteEntity $note): void;

    public function delete(AbstractNoteEntity $note): void;

    public function findOneBy(array $criteria, ?array $orderBy = null);
}
