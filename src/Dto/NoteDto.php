<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class NoteDto
{
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 10,
        max: 5000,
    )]
    public string $content;
}
