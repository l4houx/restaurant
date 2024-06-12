<?php

namespace App\Security;

use App\Repository\AdministratorRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\User\SuperAdministratorRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class AdminAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    final public const LOGIN_ROUTE = 'admin_dashboard_signin';
    private ?Passport $lastPassport = null;

    public function __construct(
        private readonly SuperAdministratorRepository $superAdministratorRepository,
        private UrlGeneratorInterface $urlGenerator,
        private readonly UrlMatcherInterface $urlMatcher
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->getPayload()->getString('username', '');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        $this->lastPassport = new Passport(
            new UserBadge($username, fn (string $identifier) => $this->superAdministratorRepository->findUserByEmailOrUsername($identifier)),
            new PasswordCredentials($request->getPayload()->getString('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );

        return $this->lastPassport;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?RedirectResponse
    {
        if ($redirect = $request->getPayload()->getString('redirect')) {
            try {
                $this->urlMatcher->match($redirect);

                return new RedirectResponse($redirect);
            } catch (\Exception) {
                // Do nothing
            }
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('admin_dashboard_index'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
