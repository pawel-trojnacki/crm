<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    public function __construct(
        private RouterInterface $router,
        private FlashBagInterface $flashBag,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app_login'
            && $request->getMethod() === 'POST';
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $userBadge = new UserBadge($email);

        return new Passport(
            $userBadge,
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->get('csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        /** @var User $user */
        $user = $token->getUser();

        $workspace = $user->getWorkspace();

        if (
            !$this->authorizationChecker->isGranted('ROLE_ADMIN')
            ||
            !$this->authorizationChecker->isGranted('ROLE_ADMIN')
        ) {
            $this->flashBag->add(
                'warning',
                <<<EOD
                Hello! You are logged in as a user with view capabilities only.
                Please notice that you are able to see almost everything,
                but you won't be able to create new contacts, deals, companies,
                meetings, users etc. or edit or delete existing ones.
                EOD,
            );
        }

        return new RedirectResponse(
            $this->getTargetPath($request->getSession(), $firewallName)
                ?? $this->router->generate('app_dashboard_index', [
                    'slug' => $workspace->getSlug(),
                ])
        );
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): ?Response {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        $request->getSession()->set(Security::LAST_USERNAME, $request->request->get('email'));

        return new RedirectResponse(
            $this->router->generate('app_login')
        );
    }
}
