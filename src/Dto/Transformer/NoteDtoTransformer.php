<?php

namespace App\Dto\Transformer;

use App\Dto\NoteDto;
use App\Dto\Transformer\Interface\DtoTransformerInterface;
use App\Entity\Abstract\AbstractNoteEntity;

class NoteDtoTransformer implements DtoTransformerInterface
{
    public function transformFromObject(AbstractNoteEntity $note): NoteDto
    {
        $noteDto = new NoteDto();

        $noteDto->content = $note->getContent();

        return $noteDto;
    }
}
