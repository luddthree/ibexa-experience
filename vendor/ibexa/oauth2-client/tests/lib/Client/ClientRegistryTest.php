<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\OAuth2Client\Client;

use Ibexa\Contracts\OAuth2Client\Exception\Client\DisabledClientException;
use Ibexa\Contracts\OAuth2Client\Exception\Client\UnavailableClientException;
use Ibexa\OAuth2Client\Client\ClientRegistry;
use Ibexa\OAuth2Client\Config\OAuth2ConfigurationInterface;
use InvalidArgumentException;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry as KnpClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use PHPUnit\Framework\TestCase;

final class ClientRegistryTest extends TestCase
{
    private const EXAMPLE_OAUTH2_PROVIDERS = ['google', 'microsoft', 'github'];
    private const EXAMPLE_AVAILABLE_CLIENT = 'google';
    private const EXAMPLE_UNAVAILABLE_CLIENT = 'apple';

    /** @var \KnpU\OAuth2ClientBundle\Client\ClientRegistry */
    private $innerClientRegistry;

    protected function setUp(): void
    {
        $this->innerClientRegistry = $this->createMock(KnpClientRegistry::class);
    }

    public function testGetClient(): void
    {
        $configuration = new OAuth2Configuration(true, self::EXAMPLE_OAUTH2_PROVIDERS);

        $expectedClient = $this->createMock(OAuth2ClientInterface::class);

        $this->innerClientRegistry
            ->method('getClient')
            ->with(self::EXAMPLE_AVAILABLE_CLIENT)
            ->willReturn($expectedClient);

        $clientRegistry = $this->createClientRegistry($configuration);

        self::assertEquals(
            $expectedClient,
            $clientRegistry->getClient(self::EXAMPLE_AVAILABLE_CLIENT)
        );
    }

    public function testGetClientThrowsDisabledClientException(): void
    {
        $this->expectException(DisabledClientException::class);

        $configuration = new OAuth2Configuration(false, self::EXAMPLE_OAUTH2_PROVIDERS);

        $clientRegistry = $this->createClientRegistry($configuration);
        $clientRegistry->getClient('google');
    }

    public function testGetClientThrowsUnavailableClientException(): void
    {
        $this->expectException(UnavailableClientException::class);

        $configuration = new OAuth2Configuration(true, self::EXAMPLE_OAUTH2_PROVIDERS);

        $clientRegistry = $this->createClientRegistry($configuration);
        $clientRegistry->getClient('not-available');
    }

    public function testGetClientHandleInvalidArgumentException(): void
    {
        $this->expectException(UnavailableClientException::class);

        $this->innerClientRegistry
            ->method('getClient')
            ->with(self::EXAMPLE_AVAILABLE_CLIENT)
            ->willThrowException(new InvalidArgumentException());

        $configuration = new OAuth2Configuration(true, self::EXAMPLE_OAUTH2_PROVIDERS);

        $clientRegistry = $this->createClientRegistry($configuration);
        $clientRegistry->getClient(self::EXAMPLE_AVAILABLE_CLIENT);
    }

    /**
     * @dataProvider dataProviderForHasClient
     */
    public function testHasClient(
        OAuth2ConfigurationInterface $configuration,
        string $identifier,
        bool $expectedResult
    ): void {
        $clientRegistry = $this->createClientRegistry($configuration);

        self::assertEquals(
            $expectedResult,
            $clientRegistry->hasClient($identifier)
        );
    }

    public function dataProviderForHasClient(): iterable
    {
        yield 'disabled oauth2' => [
            new OAuth2Configuration(false, self::EXAMPLE_OAUTH2_PROVIDERS),
            self::EXAMPLE_AVAILABLE_CLIENT,
            false,
        ];

        yield 'available client' => [
            new OAuth2Configuration(true, self::EXAMPLE_OAUTH2_PROVIDERS),
            self::EXAMPLE_AVAILABLE_CLIENT,
            true,
        ];

        yield 'not available client' => [
            new OAuth2Configuration(true, self::EXAMPLE_OAUTH2_PROVIDERS),
            self::EXAMPLE_UNAVAILABLE_CLIENT,
            false,
        ];
    }

    public function createClientRegistry(
        OAuth2ConfigurationInterface $configuration
    ): ClientRegistry {
        return new ClientRegistry($this->innerClientRegistry, $configuration);
    }
}

class_alias(ClientRegistryTest::class, 'Ibexa\Platform\Tests\OAuth2Client\Client\ClientRegistryTest');
