<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\Client\Config;

use Ibexa\Elasticsearch\ElasticSearch\Client\Config\Host;
use PHPUnit\Framework\TestCase;

final class HostTest extends TestCase
{
    public function testFromString(): void
    {
        $this->assertEquals(
            new Host('es.host', 9201, 'https', '/', 'username', 'password'),
            Host::fromString('https://username:password@es.host:9201/')
        );
    }

    /**
     * @dataProvider dataProviderForFromArray
     */
    public function testFromArray(array $properties, Host $expectedValue): void
    {
        $this->assertEquals($expectedValue, Host::fromArray($properties));
    }

    public function dataProviderForFromArray(): iterable
    {
        yield 'min' => [
            [],
            new Host(),
        ];

        yield 'full' => [
            [
                'host' => 'es.host',
                'port' => 9201,
                'scheme' => 'https',
                'path' => '/',
                'user' => 'username',
                'pass' => 'password',
            ],
            new Host('es.host', 9201, 'https', '/', 'username', 'password'),
        ];
    }
}

class_alias(HostTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\Client\Config\HostTest');
