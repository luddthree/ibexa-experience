<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Config;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException;
use Ibexa\ProductCatalog\Config\ConfigProvider;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    private const EXPECTED_ENGINE_ALIAS = 'default';
    private const EXPECTED_ENGINE_TYPE = 'local';
    private const EXPECTED_OPTIONS = [
        'foo' => 'foo',
        'bar' => 'bar',
        'baz' => 'baz',
    ];

    public function testGetEngineAlias(): void
    {
        $configProvider = $this->createConfigProviderWithValidConfiguration();

        self::assertEquals(
            self::EXPECTED_ENGINE_ALIAS,
            $configProvider->getEngineAlias(),
        );
    }

    public function testGetEngineType(): void
    {
        $configProvider = $this->createConfigProviderWithValidConfiguration();

        self::assertEquals(
            self::EXPECTED_ENGINE_TYPE,
            $configProvider->getEngineType(),
        );
    }

    public function testGetEngineTypeThrowsConfigurationExceptionForMissingEngine(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Missing product catalog engine configuration');

        $configProvider = $this->createConfigProviderWithMissingEngine();
        $configProvider->getEngineType();
    }

    public function testGetEngineTypeThrowsConfigurationExceptionForInvalidAlias(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Invalid product catalog engine alias: "invalid". Expected one of: "default"');

        $configProvider = $this->createConfigProviderWithInvalidEngine();
        $configProvider->getEngineType();
    }

    public function testGetEngineOption(): void
    {
        $configProvider = $this->createConfigProviderWithValidConfiguration();

        self::assertEquals(
            self::EXPECTED_OPTIONS['foo'],
            $configProvider->getEngineOption('foo')
        );

        self::assertNull(
            $configProvider->getEngineOption('foobar')
        );

        self::assertEquals(
            'default',
            $configProvider->getEngineOption('foobar', 'default')
        );
    }

    public function testGetEngineOptionThrowsConfigurationExceptionForMissingEngine(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Missing product catalog engine configuration');

        $configProvider = $this->createConfigProviderWithMissingEngine();
        $configProvider->getEngineOption('foo');
    }

    public function testGetEngineOptionsThrowsConfigurationExceptionForInvalidAlias(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Invalid product catalog engine alias: "invalid". Expected one of: "default"');

        $configProvider = $this->createConfigProviderWithInvalidEngine();
        $configProvider->getEngineOption('foo');
    }

    private function createConfigProviderWithMissingEngine(): ConfigProvider
    {
        return $this->createConfigProvider(null);
    }

    private function createConfigProviderWithInvalidEngine(): ConfigProvider
    {
        return $this->createConfigProvider('invalid');
    }

    private function createConfigProviderWithValidConfiguration(): ConfigProvider
    {
        return $this->createConfigProvider(self::EXPECTED_ENGINE_ALIAS);
    }

    private function createConfigProvider(?string $engine): ConfigProvider
    {
        return new ConfigProvider(
            $this->createRepositoryConfigurationProvider($engine),
            [
                self::EXPECTED_ENGINE_ALIAS => [
                    'type' => self::EXPECTED_ENGINE_TYPE,
                    'options' => self::EXPECTED_OPTIONS,
                ],
            ]
        );
    }

    private function createRepositoryConfigurationProvider(?string $alias): RepositoryConfigurationProvider
    {
        $config = [
            'product_catalog' => [
                'engine' => $alias,
            ],
        ];

        $repositoryConfigurationProvider = $this->createMock(RepositoryConfigurationProvider::class);
        $repositoryConfigurationProvider->method('getRepositoryConfig')->willReturn($config);

        return $repositoryConfigurationProvider;
    }
}
