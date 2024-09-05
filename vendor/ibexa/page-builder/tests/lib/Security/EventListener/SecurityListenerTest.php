<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\PageBuilder\Security\EventListener;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Core\MVC\Symfony\Security\UserInterface;
use Ibexa\PageBuilder\Security\EditorialMode\PostAuthenticationGuardToken;
use Ibexa\PageBuilder\Security\EventListener\SecurityListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent as BaseInteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class SecurityListenerTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $permissionResolver;

    /** @var \Ibexa\PageBuilder\Security\EventListener\SecurityListener */
    private $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->permissionResolver = $this->createMock(PermissionResolver::class);
        $this->listener = $this->generateListener();
    }

    public function testGetSubscribedEvents(): void
    {
        $this->assertSame(
            [
                SecurityEvents::INTERACTIVE_LOGIN => [
                    ['onInteractiveLogin', 10],
                ],
            ],
            SecurityListener::getSubscribedEvents()
        );
    }

    public function testOnInteractiveLogin(): void
    {
        $apiUser = $this->createMock(User::class);
        $user = $this->createMock(UserInterface::class);
        $user
            ->expects($this->once())
            ->method('getAPIUser')
            ->willReturn($apiUser);
        $token = $this->createMock(PostAuthenticationGuardToken::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($user));
        $event = new BaseInteractiveLoginEvent(new Request(), $token);

        $this->listener->onInteractiveLogin($event);
    }

    public function testOnInteractiveLoginNotUserObject(): void
    {
        $user = 'user';
        $token = $this->createMock(PostAuthenticationGuardToken::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($user));
        $event = new BaseInteractiveLoginEvent(new Request(), $token);

        $this->permissionResolver
            ->expects($this->never())
            ->method('setCurrentUserReference');

        $this->listener->onInteractiveLogin($event);
    }

    protected function generateListener(): SecurityListener
    {
        return new SecurityListener(
            $this->permissionResolver
        );
    }
}

class_alias(SecurityListenerTest::class, 'EzSystems\EzPlatformPageBuilder\Tests\Security\EventListener\SecurityListenerTest');
