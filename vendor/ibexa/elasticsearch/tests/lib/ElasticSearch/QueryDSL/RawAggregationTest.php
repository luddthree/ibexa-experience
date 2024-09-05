<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\QueryDSL;

use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\RawAggregation;
use PHPUnit\Framework\TestCase;

final class RawAggregationTest extends TestCase
{
    private const EXAMPLE_RAW_VALUE = [
        'terms' => [
            'foo' => 'bar',
        ],
    ];

    public function testToArray(): void
    {
        $this->assertEquals(
            self::EXAMPLE_RAW_VALUE,
            (new RawAggregation(self::EXAMPLE_RAW_VALUE))->toArray()
        );
    }
}

class_alias(RawAggregationTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\QueryDSL\RawAggregationTest');
