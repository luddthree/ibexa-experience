<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\Client\Config;

use Ibexa\Elasticsearch\ElasticSearch\Client\Config\SSLCACert;
use PHPUnit\Framework\TestCase;

final class SSLCACertTest extends TestCase
{
    private const EXAMPLE_PATH = '/etc/certs/ca_cert.crt';

    public function testFromArray(): void
    {
        $properties = [
            'path' => self::EXAMPLE_PATH,
        ];

        $this->assertEquals(new SSLCACert(self::EXAMPLE_PATH), SSLCACert::fromArray($properties));
    }
}

class_alias(SSLCACertTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\Client\Config\SSLCACertTest');
