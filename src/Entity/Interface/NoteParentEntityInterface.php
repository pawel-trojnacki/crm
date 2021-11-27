<?php

namespace App\Entity\Interface;

use App\Entity\Workspace;
use Doctrine\Common\Collections\Collection;

interface NoteParentEntityInterface
{
    public function getNotes(): Collection;

    public function getWorkspace(): ?Workspace;

    public function getSlug(): ?string;
}
