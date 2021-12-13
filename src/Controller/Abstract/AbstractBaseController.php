<?php

namespace App\Controller\Abstract;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class AbstractBaseController extends AbstractController
{
    protected function getUser(): ?User
    {
        return parent::getUser();
    }

    protected function redirectToReferer(
        ?string $referer,
        string $defaultRoute = '',
        array $parameters = [],
    ): RedirectResponse {
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute($defaultRoute, $parameters);
    }

    protected function addFlashSuccess(string $message): void
    {
        $this->addFlash('success', $message);
    }

    protected function addFlashWarning(string $message): void
    {
        $this->addFlash('warning', $message);
    }
}
