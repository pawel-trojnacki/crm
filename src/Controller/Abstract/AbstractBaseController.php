<?php

namespace App\Controller\Abstract;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class AbstractBaseController extends AbstractController
{
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
}
