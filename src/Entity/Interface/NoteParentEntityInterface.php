<?php

namespace App\Entity\Interface;

use Doctrine\Common\Collections\Collection;

interface NoteParentEntityInterface
{
    public function getNotes(): Collection;
}
