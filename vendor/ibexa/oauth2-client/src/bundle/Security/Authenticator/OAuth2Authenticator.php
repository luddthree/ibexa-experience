<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\OAuth2Client\Security\Authenticator;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference;
use Ibexa\Contracts\OAuth2Client\Client\ClientRegistry;
use Ibexa\Contracts\OAuth2Client\ResourceOwner\ResourceOwnerMapperRegistry;
use Ibexa\Core\MVC\Symfony\Security\User as SecurityUser;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class OAuth2Authenticator extends SocialAuthenticator
{
    /** @var \Ibexa\Contracts\OAuth2Client\Client\ClientRegistry */
    private $clientRegistry;

    /** @var \Ibexa\Contracts\OAuth2Client\ResourceOwner\ResourceOwnerMapperRegistry */
    private $resourceOwnerMapperRegistry;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var string */
    private $checkRouteName;

    /** @var string */
    private $loginRouteName;

    public function __construct(
        ClientRegistry $clientRegistry,
        ResourceOwnerMapperRegistry $resourceOwnerMapperRegistry,
        UrlGeneratorInterface $urlGenerator,
        PermissionResolver $permissionResolver,
        string $checkRouteName,
        string $loginRouteName
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->resourceOwnerMapperRegistry = $resourceOwnerMapperRegistry;
        $this->urlGenerator = $urlGenerator;
        $this->permissionResolver = $permissionResolver;
        $this->checkRouteName = $checkRouteName;
        $this->loginRouteName = $loginRouteName;
    }

    public function supports(Request $request): bool
    {
        if ($request->attributes->get('_route') !== $this->checkRouteName) {
            return false;
        }

        return true;
    }

    public function getCredentials(Request $request): OAuth2Credentials
    {
        $identifier = $this->getIdentifier($request);
        $client = $this->clientRegistry->getClient($identifier);

        return new OAuth2Credentials($identifier, $this->fetchAccessToken($client));
    }

    /**
     * @param \Ibexa\OAuth2Client\Security\Authenticator\OAuth2Credentials $credentials
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $client = $this->clientRegistry->getClient($credentials->getIdentifier());

        $resourceOwner = $client->fetchUserFromToken(
            $credentials->getAccessToken()
        );

        $mapper = $this->resourceOwnerMapperRegistry->getResourceOwnerMapper(
            $credentials->getIdentifier()
        );

        return $mapper->getUser($resourceOwner, $userProvider);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            $this->urlGenerator->generate($this->loginRouteName),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if ($user instanceof SecurityUser) {
            if ($user->getAPIUser() instanceof UserReference) {
                $this->permissionResolver->setCurrentUserReference($user->getAPIUser());
            }
        }

        // do nothing - the fact that the access token works is enough
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        $previousUrl = $this->getPreviousUrl($request, $providerKey);
        if ($previousUrl !== null) {
            return new RedirectResponse($previousUrl);
        }

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    private function getIdentifier(Request $request): string
    {
        return $request->attributes->get('identifier');
    }
}

class_alias(OAuth2Authenticator::class, 'Ibexa\Platform\Bundle\OAuth2Client\Security\Authenticator\OAuth2Authenticator');
