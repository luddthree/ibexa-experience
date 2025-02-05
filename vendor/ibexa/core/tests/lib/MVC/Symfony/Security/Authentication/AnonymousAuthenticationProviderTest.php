<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Security\Authentication;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Security\Authentication\AnonymousAuthenticationProvider;
use Ibexa\Core\Repository\Values\User\UserReference;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\User\UserInterface;

class AnonymousAuthenticationProviderTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $permissionResolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->permissionResolver = $this->createMock(PermissionResolver::class);
    }

    public function testAuthenticate()
    {
        $anonymousUserId = 10;
        $this->configResolver
            ->expects($this->once())
            ->method('getParameter')
            ->with('anonymous_user_id')
            ->will($this->returnValue($anonymousUserId));

        $this->permissionResolver
            ->expects($this->once())
            ->method('setCurrentUserReference')
            ->with(new UserReference($anonymousUserId));

        $key = 'some_key';
        $authProvider = new AnonymousAuthenticationProvider($key);
        $authProvider->setPermissionResolver($this->permissionResolver);
        $authProvider->setConfigResolver($this->configResolver);
        $anonymousToken = $this
            ->getMockBuilder(AnonymousToken::class)
            ->setConstructorArgs([$key, $this->createMock(UserInterface::class)])
            ->getMockForAbstractClass();
        $this->assertSame($anonymousToken, $authProvider->authenticate($anonymousToken));
    }
}

class_alias(AnonymousAuthenticationProviderTest::class, 'eZ\Publish\Core\MVC\Symfony\Security\Tests\Authentication\AnonymousAuthenticationProviderTest');
