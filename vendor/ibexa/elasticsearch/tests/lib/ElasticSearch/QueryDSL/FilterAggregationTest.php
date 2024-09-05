<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\QueryDSL;

use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Aggregation;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\FilterAggregation;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Query;
use PHPUnit\Framework\TestCase;

final class FilterAggregationTest extends TestCase
{
    /**
     * @dataProvider dataProviderForToArray
     */
    public function testToArray(FilterAggregation $aggregation, array $expectedValue): void
    {
        $this->assertEquals($expectedValue, $aggregation->toArray());
    }

    public function dataProviderForToArray(): iterable
    {
        $filterQuery = $this->createMock(Query::class);
        $filterQuery->method('toArray')->willReturn([
            'term' => [
                'section_id_id' => 2,
            ],
        ]);

        $innerAggregation = $this->createMock(Aggregation::class);
        $innerAggregation->method('toArray')->willReturn([
            'terms' => [
                'foo' => 'bar',
            ],
        ]);

        $expectedResult = [
            'filter' => [
                'term' => [
                    'section_id_id' => 2,
                ],
            ],
            'aggs' => [
                'inner' => [
                    'terms' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
        ];

        yield 'constructor' => [
            new FilterAggregation($filterQuery, [
                'inner' => $innerAggregation,
            ]),
            $expectedResult,
        ];

        yield 'setters' => [
            (new FilterAggregation())->withQuery($filterQuery)->addAggregation('inner', $innerAggregation),
            $expectedResult,
        ];
    }
}

class_alias(FilterAggregationTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\QueryDSL\FilterAggregationTest');
