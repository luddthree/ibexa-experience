<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\Client;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Elasticsearch\ElasticSearch\Client\ClientConfigurationProvider;
use Ibexa\Elasticsearch\ElasticSearch\Client\Config\Client as ClientConfig;
use PHPUnit\Framework\TestCase;

final class ClientConfigurationProviderTest extends TestCase
{
    private const EXAMPLE_CONFIGURATIONS = [
        'localhost' => [
            'hosts' => [
                [
                    'host' => 'localhost',
                    'port' => 9200,
                    'scheme' => 'http',
                    'path' => null,
                    'user' => null,
                    'pass' => null,
                ],
            ],
        ],
        'cloud' => [
            'elastic_cloud_id' => 'prod:CAIcgKYcPl5khqYbCV5oeJIcgJomOZQve3Y',
            'authentication' => [
                'type' => 'api_key',
                'credentials' => ['xsO6OkinYtYPesCP4RFwAc', '8Sy5t3WPUEzKx6j4A7nU'],
            ],
        ],
    ];

    public function testGetClientConfigurationDefault(): void
    {
        $configurationProvider = new ClientConfigurationProvider(self::EXAMPLE_CONFIGURATIONS, 'localhost');

        $this->assertEquals(
            ClientConfig::fromArray(self::EXAMPLE_CONFIGURATIONS['localhost']),
            $configurationProvider->getClientConfiguration()
        );
    }

    public function testGetClientConfigurationByName(): void
    {
        $configurationProvider = new ClientConfigurationProvider(self::EXAMPLE_CONFIGURATIONS, 'localhost');

        $this->assertEquals(
            ClientConfig::fromArray(self::EXAMPLE_CONFIGURATIONS['cloud']),
            $configurationProvider->getClientConfiguration('cloud')
        );
    }

    public function testGetClientConfigurationInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $configurationProvider = new ClientConfigurationProvider(self::EXAMPLE_CONFIGURATIONS, 'localhost');
        $configurationProvider->getClientConfiguration('invalid');
    }
}

class_alias(ClientConfigurationProviderTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\Client\ClientConfigurationProviderTest');
