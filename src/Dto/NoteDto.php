<?php

namespace App\Dto;

use App\Entity\Abstract\AbstractNoteEntity;
use Symfony\Component\Validator\Constraints as Assert;

class NoteDto {
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 10,
        max: 5000,
    )]
    public string $content;

    public static function createFromNoteEntity(AbstractNoteEntity $nooteEntity): self
    {
        $note = new self();

        $note->content = $nooteEntity->getContent();

        return $note;
    }
}