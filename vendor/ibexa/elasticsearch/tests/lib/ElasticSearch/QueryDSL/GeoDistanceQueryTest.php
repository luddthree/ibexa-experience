<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\QueryDSL;

use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\GeoDistanceQuery;
use PHPUnit\Framework\TestCase;

final class GeoDistanceQueryTest extends TestCase
{
    /**
     * @dataProvider dataProviderForToArray
     */
    public function testToArray(GeoDistanceQuery $query, array $expectedValue): void
    {
        $this->assertEquals($expectedValue, $query->toArray());
    }

    public function dataProviderForToArray(): iterable
    {
        yield 'constructor' => [
            new GeoDistanceQuery('foo', '10km', 50.06143, 19.93658),
            [
                'geo_distance' => [
                    'distance' => '10km',
                    'foo' => [
                        'lat' => 50.06143,
                        'lon' => 19.93658,
                    ],
                    'ignore_unmapped' => true,
                ],
            ],
        ];

        yield 'setters' => [
            (new GeoDistanceQuery())
                ->withFieldName('foo')
                ->withDistance('10km')
                ->withLat(50.06143)
                ->withLon(19.93658),
            [
                'geo_distance' => [
                    'distance' => '10km',
                    'foo' => [
                        'lat' => 50.06143,
                        'lon' => 19.93658,
                    ],
                    'ignore_unmapped' => true,
                ],
            ],
        ];
    }
}

class_alias(GeoDistanceQueryTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\QueryDSL\GeoDistanceQueryTest');
