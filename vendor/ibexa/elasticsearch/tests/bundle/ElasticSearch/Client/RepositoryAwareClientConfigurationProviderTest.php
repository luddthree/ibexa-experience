<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Elasticsearch\ElasticSearch\Client;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Bundle\Elasticsearch\ElasticSearch\Client\RepositoryAwareClientConfigurationProvider;
use Ibexa\Elasticsearch\ElasticSearch\Client\ClientConfigurationProviderInterface;
use Ibexa\Elasticsearch\ElasticSearch\Client\Config\Client;
use Ibexa\Elasticsearch\ElasticSearch\Client\Config\Host;
use PHPUnit\Framework\TestCase;

final class RepositoryAwareClientConfigurationProviderTest extends TestCase
{
    private const EXAMPLE_CONFIG_NAME = 'intranet';

    /** @var \Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider|\PHPUnit\Framework\MockObject\MockObject */
    private $repositoryConfigurationProvider;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\Client\ClientConfigurationProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $innerConfigurationProvider;

    /** @var \Ibexa\Bundle\Elasticsearch\ElasticSearch\Client\RepositoryAwareClientConfigurationProvider */
    private $configurationProvider;

    protected function setUp(): void
    {
        $this->repositoryConfigurationProvider = $this->createMock(RepositoryConfigurationProvider::class);
        $this->innerConfigurationProvider = $this->createMock(ClientConfigurationProviderInterface::class);
        $this->configurationProvider = new RepositoryAwareClientConfigurationProvider(
            $this->innerConfigurationProvider,
            $this->repositoryConfigurationProvider
        );
    }

    public function testGetSpecificClientConfiguration(): void
    {
        $expectedConfig = $this->getExampleClientConfig();

        $this->repositoryConfigurationProvider
            ->expects($this->never())
            ->method('getRepositoryConfig');

        $this->innerConfigurationProvider
            ->method('getClientConfiguration')
            ->with(self::EXAMPLE_CONFIG_NAME)
            ->willReturn($expectedConfig);

        $this->assertEquals(
            $expectedConfig,
            $this->configurationProvider->getClientConfiguration(
                self::EXAMPLE_CONFIG_NAME
            )
        );
    }

    public function testGetDefaultClientConfiguration(): void
    {
        $expectedConfig = $this->getExampleClientConfig();

        $this->repositoryConfigurationProvider
            ->method('getRepositoryConfig')
            ->willReturn([
                'search' => [
                    'connection' => self::EXAMPLE_CONFIG_NAME,
                ],
            ]);

        $this->innerConfigurationProvider
            ->method('getClientConfiguration')
            ->with(self::EXAMPLE_CONFIG_NAME)
            ->willReturn($expectedConfig);

        $this->assertEquals(
            $expectedConfig,
            $this->configurationProvider->getClientConfiguration()
        );
    }

    private function getExampleClientConfig(): Client
    {
        return new Client([
            new Host('localhost', 9200, 'http'),
        ]);
    }
}

class_alias(RepositoryAwareClientConfigurationProviderTest::class, 'Ibexa\Platform\Bundle\ElasticSearchEngine\Tests\ElasticSearch\Client\RepositoryAwareClientConfigurationProviderTest');
