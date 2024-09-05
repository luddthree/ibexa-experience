<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Security\EditorialMode;

use Ibexa\AdminUi\Specification\SiteAccess\IsAdmin;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /** @var \Symfony\Component\Security\Core\Security */
    private $security;

    /** @var \Ibexa\PageBuilder\Security\EditorialMode\TokenManager */
    private $tokenManager;

    /** @var \Ibexa\PageBuilder\Security\EditorialMode\TokenRevokerInterface */
    private $tokenRevoker;

    /** @var bool */
    private $enabled;

    /** @var array */
    private $siteAccessGroups;

    /** @var string */
    private $tokenQueryParamName;

    /** @var string */
    private $tokenCookieName;

    public function __construct(
        Security $security,
        TokenManager $tokenManager,
        TokenRevokerInterface $tokenRevoker,
        string $tokenQueryParamName,
        string $tokenCookieName,
        bool $enabled,
        array $siteAccessGroups
    ) {
        $this->security = $security;
        $this->tokenManager = $tokenManager;
        $this->tokenRevoker = $tokenRevoker;
        $this->tokenQueryParamName = $tokenQueryParamName;
        $this->tokenCookieName = $tokenCookieName;
        $this->enabled = $enabled;
        $this->siteAccessGroups = $siteAccessGroups;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
    }

    public function supports(Request $request): bool
    {
        if (!$this->isEnabled() || $this->isAdminSiteAccess($request)) {
            return false;
        }

        if (!$this->hasToken($request)) {
            return false;
        }

        if ($this->security->getUser() && !$this->isUserSwitch($request)) {
            return false;
        }

        return true;
    }

    public function getCredentials(Request $request)
    {
        $rawToken = $request->query->get($this->getTokenQueryParamName())
            ?? $request->cookies->get($this->getTokenCookieName());

        try {
            $token = $this->tokenManager->decodeFromString($rawToken);
            if ($token === false) {
                throw new InvalidTokenException('Invalid JWT Token');
            }

            return $token;
        } catch (JWTDecodeFailureException $e) {
            if (JWTDecodeFailureException::EXPIRED_TOKEN === $e->getReason()) {
                throw new ExpiredTokenException();
            }

            throw new InvalidTokenException('Invalid JWT Token', 0, $e);
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials) {
            // The token header was empty, authentication fails with 401
            return null;
        }

        $username = $credentials[$this->tokenManager->getUserIdentityField()];

        return $userProvider->loadUserByUsername($username);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($this->tokenRevoker->isValid($credentials)) {
            $this->tokenRevoker->revoke($credentials);

            return true;
        }

        return false;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    private function isUserSwitch(Request $request): bool
    {
        $rawToken = $request->query->get($this->getTokenQueryParamName())
            ?? $request->cookies->get($this->getTokenCookieName());

        try {
            $token = $this->tokenManager->decodeFromString($rawToken);
            if ($token === false) {
                return false;
            }

            $currentUsername = $this->security->getUser()->getUsername();
            $tokenUsername = $token[$this->tokenManager->getUserIdentityField()];

            return $currentUsername !== $tokenUsername;
        } catch (JWTDecodeFailureException $e) {
            return false;
        }
    }

    private function hasToken(Request $request): bool
    {
        $hasTokenInQueryParam = $request->query->has($this->getTokenQueryParamName());
        $hasTokenInCookie = $request->cookies->has($this->getTokenCookieName());

        return $hasTokenInQueryParam || $hasTokenInCookie;
    }

    /**
     * Returns true if token based authentication is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Returns name of query parameter used to pass token.
     */
    public function getTokenQueryParamName(): string
    {
        return $this->tokenQueryParamName;
    }

    /**
     * Returns name of cookie used to pass token.
     */
    public function getTokenCookieName(): string
    {
        return $this->tokenCookieName;
    }

    /**
     * Create an authenticated token for the given user.
     */
    public function createAuthenticatedToken(UserInterface $user, string $providerKey): PostAuthenticationGuardToken
    {
        return new PostAuthenticationGuardToken(
            $user,
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * Returns true if the siteaccess of given request is administrative.
     */
    private function isAdminSiteAccess(Request $request): bool
    {
        $siteaccess = $request->attributes->get('siteaccess', null);
        if ($siteaccess instanceof SiteAccess) {
            return (new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($siteaccess);
        }

        return false;
    }
}

class_alias(TokenAuthenticator::class, 'EzSystems\EzPlatformPageBuilder\Security\EditorialMode\TokenAuthenticator');
