<?php

namespace App\Dto;

use App\Entity\Contact;
use App\Validator\MeetingEndTime as MeetingEndTimeAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[MeetingEndTimeAssert]
class MeetingDto
{
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 50,
    )]
    public string $name;

    #[Assert\NotBlank]
    public int $importance;

    #[Assert\NotBlank]
    #[Assert\Type(\DateTime::class)]
    #[Assert\GreaterThan(
        value: 'today UTC',
        message: 'It looks like you are trying to go back in time'
    )]
    public \DateTime $beginAt;

    #[Assert\Type(\DateTime::class)]
    public ?\DateTime $endAt = null;

    public ?Contact $contact = null;
}
