<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\Client\Config;

use Ibexa\Elasticsearch\ElasticSearch\Client\Config\Authentication;
use Ibexa\Elasticsearch\ElasticSearch\Client\Config\Client;
use Ibexa\Elasticsearch\ElasticSearch\Client\Config\Host;
use Ibexa\Elasticsearch\ElasticSearch\Client\Config\SSL;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    private const EXAMPLE_ELASTIC_CLOUD_ID = 'test:1htFY83VvX2JRDw88MOkOejk';

    /**
     * @dataProvider dataProviderForFromArray
     */
    public function testFromArray(array $properties, Client $expectedValue): void
    {
        $this->assertEquals($expectedValue, Client::fromArray($properties));
    }

    public function dataProviderForFromArray(): iterable
    {
        yield 'min' => [
            [],
            new Client(),
        ];

        yield 'full' => [
            [
                'hosts' => [
                    [
                        'dsn' => 'https://a.es.host',
                    ],
                    [
                        'host' => 'b.es.host',
                        'scheme' => 'https',
                    ],
                ],
                'elastic_cloud_id' => self::EXAMPLE_ELASTIC_CLOUD_ID,
                'authentication' => [
                    'type' => Authentication::TYPE_BASIC,
                    'credentials' => ['username', 'password'],
                ],
                'ssl' => [
                    'verification' => true,
                ],
                'connection_pool' => 'App\ElasticSearch\CustomConnectionPool',
                'connection_selector' => 'App\ElasticSearch\CustomConnectionSelector',
                'retries' => 3,
                'index_templates' => ['default'],
                'debug' => true,
                'trace' => true,
            ],
            new Client(
                [
                    new Host('a.es.host', Host::DEFAULT_PORT, 'https'),
                    new Host('b.es.host', Host::DEFAULT_PORT, 'https'),
                ],
                self::EXAMPLE_ELASTIC_CLOUD_ID,
                new Authentication(
                    Authentication::TYPE_BASIC,
                    ['username', 'password']
                ),
                new SSL(true),
                'App\ElasticSearch\CustomConnectionPool',
                'App\ElasticSearch\CustomConnectionSelector',
                3,
                ['default'],
                true,
                true
            ),
        ];
    }
}

class_alias(ClientTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\Client\Config\ClientTest');
