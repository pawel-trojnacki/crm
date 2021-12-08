<?php

namespace App\Dto;

use App\Entity\Contact;
use App\Entity\Meeting;
use Symfony\Component\Validator\Constraints as Assert;

class MeetingDto
{
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 50,
    )]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: Meeting::IMPORTANCE_OPTIONS, message: 'Choose a valid option')]
    public string $importance;

    #[Assert\NotBlank]
    #[Assert\DateTime]
    public \DateTime $beginAt;

    public ?\DateTime $endAt = null;

    public ?Contact $contact = null;
}
