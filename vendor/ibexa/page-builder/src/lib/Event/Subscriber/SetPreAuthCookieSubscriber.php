<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event\Subscriber;

use Ibexa\Core\MVC\Symfony\Security\UserInterface;
use Ibexa\PageBuilder\Security\EditorialMode\TokenManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @internal
 */
class SetPreAuthCookieSubscriber implements EventSubscriberInterface
{
    /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    private $tokenStorage;

    /** @var \Ibexa\PageBuilder\Security\EditorialMode\TokenManager */
    private $tokenManager;

    /** @var string */
    private $tokenCookieName;

    /** @var int */
    private $ttl;

    /** @var array<string, string> */
    private $routesMap;

    /** @var bool */
    private $enabled;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        TokenManager $tokenManager,
        string $tokenCookieName,
        int $ttl,
        array $routesMap,
        bool $enabled
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->tokenManager = $tokenManager;
        $this->tokenCookieName = $tokenCookieName;
        $this->ttl = $ttl;
        $this->routesMap = $routesMap;
        $this->enabled = $enabled;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onResponse',
        ];
    }

    public function onResponse(ResponseEvent $event): void
    {
        if (!$this->enabled) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        $route = $request->attributes->get('_route');

        if (!in_array($route, $this->routesMap, true)) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if ($token === null || !$token->getUser() instanceof UserInterface) {
            return;
        }

        $jwtToken = $this->tokenManager->create($token->getUser());

        $expire = new \DateTime();
        $expire->modify(sprintf('+%d seconds', $this->ttl));

        $response->headers->setCookie(
            new Cookie(
                $this->tokenCookieName,
                $jwtToken,
                $expire,
                '/',
                null,
                true,
                true,
                false,
                'None'
            )
        );
    }
}

class_alias(SetPreAuthCookieSubscriber::class, 'EzSystems\EzPlatformPageBuilder\Event\Subscriber\SetPreAuthCookieSubscriber');
