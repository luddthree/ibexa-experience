<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\OAuth2Client\Config;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\OAuth2Client\Config\OAuth2Configuration;
use PHPUnit\Framework\TestCase;

final class OAuth2ConfigurationTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    /** @var \Ibexa\OAuth2Client\Config\OAuth2Configuration */
    private $configuration;

    protected function setUp(): void
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->configuration = new OAuth2Configuration($this->configResolver);
    }

    /**
     * @dataProvider dataProviderForIsOAuth2Enabled
     */
    public function testIsOAuth2Enabled(?bool $isEnabled): void
    {
        $this->configResolver->method('hasParameter')->with('oauth2.enabled')->willReturn($isEnabled !== null);
        $this->configResolver->method('getParameter')->with('oauth2.enabled', null, null)->willReturn($isEnabled);

        self::assertEquals(
            (bool)$isEnabled,
            $this->configuration->isOAuth2Enabled()
        );
    }

    public function dataProviderForIsOAuth2Enabled(): iterable
    {
        yield 'OAuth2 is enabled' => [true];
        yield 'OAuth2 is disabled' => [false];
        yield 'Undefined oauth2.enabled parameter' => [null];
    }

    /**
     * @dataProvider dataProviderForGetAvailableClients
     */
    public function testGetAvailableClients(?array $expectedClients): void
    {
        $this->configResolver
            ->method('hasParameter')
            ->with('oauth2.clients')
            ->willReturn($expectedClients !== null);

        $this->configResolver
            ->method('getParameter')
            ->with('oauth2.clients', null, null)
            ->willReturn($expectedClients);

        self::assertEquals(
            (array)$expectedClients,
            $this->configuration->getAvailableClients()
        );
    }

    public function dataProviderForGetAvailableClients(): iterable
    {
        yield 'Typical case' => [
            ['google', 'microsoft', 'apple'],
        ];

        yield 'Undefined oauth2.clients parameter' => [null];
    }

    public function testIsAvailable(): void
    {
        $this->configResolver->method('hasParameter')->with('oauth2.clients')->willReturn(true);
        $this->configResolver
            ->method('getParameter')
            ->with('oauth2.clients', null, null)
            ->willReturn(['google', 'microsoft', 'apple']);

        self::assertTrue($this->configuration->isAvailable('google'));
        self::assertFalse($this->configuration->isAvailable('samsung'));
    }
}

class_alias(OAuth2ConfigurationTest::class, 'Ibexa\Platform\Tests\OAuth2Client\Config\OAuth2ConfigurationTest');
