<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\Client\Config;

use Ibexa\Elasticsearch\ElasticSearch\Client\Config\SSL;
use Ibexa\Elasticsearch\ElasticSearch\Client\Config\SSLCACert;
use Ibexa\Elasticsearch\ElasticSearch\Client\Config\SSLCert;
use Ibexa\Elasticsearch\ElasticSearch\Client\Config\SSLKey;
use PHPUnit\Framework\TestCase;

final class SSLTest extends TestCase
{
    /**
     * @dataProvider dataProviderForFromArray
     */
    public function testFromArray(array $properties, SSL $expectedValue): void
    {
        $this->assertEquals($expectedValue, SSL::fromArray($properties));
    }

    public function dataProviderForFromArray(): iterable
    {
        yield 'min' => [
            [],
            new SSL(),
        ];

        yield 'full' => [
            [
                'verification' => true,
                'cert' => [
                    'path' => '/etc/certs/cert.crt',
                ],
                'cert_key' => [
                    'path' => '/etc/certs/cert_key.key',
                ],
            ],
            new SSL(true, new SSLCert('/etc/certs/cert.crt'), new SSLKey('/etc/certs/cert_key.key')),
        ];

        yield 'ca' => [
            [
                'verification' => true,
                'ca_cert' => [
                    'path' => '/etc/certs/ca_cert.crt',
                ],
            ],
            new SSL(true, null, null, new SSLCACert('/etc/certs/ca_cert.crt')),
        ];
    }
}

class_alias(SSLTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\Client\Config\SSLTest');
