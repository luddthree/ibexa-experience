<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Security\EditorialMode;

use Psr\Log\LoggerInterface;
use Symfony\Cmf\Component\Routing\ChainRouter as BaseChainRouter;
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Router responsible for decorate routes with editorial mode token.
 */
class TokenAuthorizedRouter extends BaseChainRouter implements VersatileGeneratorInterface
{
    /** @var \Ibexa\PageBuilder\Security\EditorialMode\TokenManager */
    private $jwtTokenManager;

    /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    private $tokenStorage;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \Ibexa\PageBuilder\Security\EditorialMode\TokenAuthenticator|null */
    private $authenticator;

    /** @var array */
    private $routesMap;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Ibexa\PageBuilder\Security\EditorialMode\TokenManager $jwtTokenManager
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     * @param \Ibexa\PageBuilder\Security\EditorialMode\TokenAuthenticator|null $authenticator
     * @param array $routesMap
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(
        RouterInterface $router,
        ?TokenManager $jwtTokenManager,
        TokenStorageInterface $tokenStorage,
        ?TokenAuthenticator $authenticator,
        array $routesMap,
        LoggerInterface $logger = null
    ) {
        parent::__construct($logger);

        $this->router = $router;
        $this->jwtTokenManager = $jwtTokenManager;
        $this->tokenStorage = $tokenStorage;
        $this->authenticator = $authenticator;
        $this->routesMap = $routesMap;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string
    {
        if ($this->authenticator !== null && $this->authenticator->isEnabled()) {
            $token = $this->tokenStorage->getToken();

            if ($token !== null) {
                $parameters[$this->authenticator->getTokenQueryParamName()] = $this->jwtTokenManager->create(
                    $token->getUser()
                );
            }
        }

        return $this->router->generate($this->routesMap[$name], $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name): bool
    {
        if (is_string($name)) {
            return isset($this->routesMap[$name]);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteDebugMessage($name, array $parameters = []): string
    {
        return "Route '$name' not found";
    }
}

class_alias(TokenAuthorizedRouter::class, 'EzSystems\EzPlatformPageBuilder\Security\EditorialMode\TokenAuthorizedRouter');
