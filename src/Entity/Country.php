<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 80)]
    private $name;

    #[ORM\Column(type: 'string', length: 2)]
    private $isoCode;

    #[ORM\Column(type: 'string', length: 7)]
    private $isdCode;

    public function __construct(string $name, string $isoCode, string $isdCode)
    {
        $this->name = $name;
        $this->isoCode = $isoCode;
        $this->isdCode = $isdCode;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function getIsdCode(): string
    {
        return $this->isdCode;
    }
}
