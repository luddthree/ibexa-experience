<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\Client\Config;

use Ibexa\Elasticsearch\ElasticSearch\Client\Config\SSLCert;
use PHPUnit\Framework\TestCase;

final class SSLCertTest extends TestCase
{
    private const EXAMPLE_PATH = '/etc/certs/cert.crt';
    private const EXAMPLE_PASS = 'secret';

    /**
     * @dataProvider dataProviderForFromArray
     */
    public function testFromArray(array $properties, SSLCert $expectedValue): void
    {
        $this->assertEquals($expectedValue, SSLCert::fromArray($properties));
    }

    public function dataProviderForFromArray(): iterable
    {
        yield 'min' => [
            [
                'path' => self::EXAMPLE_PATH,
            ],
            new SSLCert(self::EXAMPLE_PATH),
        ];

        yield 'full' => [
            [
                'path' => self::EXAMPLE_PATH,
                'pass' => self::EXAMPLE_PASS,
            ],
            new SSLCert(self::EXAMPLE_PATH, self::EXAMPLE_PASS),
        ];
    }
}

class_alias(SSLCertTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\Client\Config\SSLCertTest');
