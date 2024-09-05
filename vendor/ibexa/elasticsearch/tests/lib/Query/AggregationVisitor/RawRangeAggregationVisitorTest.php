<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Range;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawRangeAggregation;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Elasticsearch\Query\AggregationVisitor\RawRangeAggregationVisitor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;

final class RawRangeAggregationVisitorTest extends AbstractAggregationVisitorTest
{
    private const EXAMPLE_AGGREGATION_NAME = 'custom_aggregation';

    protected function createVisitor(): AggregationVisitor
    {
        return new RawRangeAggregationVisitor();
    }

    public function dataProviderForTestSupports(): iterable
    {
        $emptyLanguageFilter = MockUtils::createEmptyLanguageFilter();

        yield 'true' => [
            new RawRangeAggregation(
                self::EXAMPLE_AGGREGATION_NAME,
                self::EXAMPLE_SEARCH_INDEX_FIELD,
                []
            ),
            $emptyLanguageFilter,
            true,
        ];

        yield 'false' => [
            $this->createMock(Aggregation::class),
            $emptyLanguageFilter,
            false,
        ];
    }

    public function dataProviderForTestVisit(): iterable
    {
        $emptyLanguageFilter = MockUtils::createEmptyLanguageFilter();

        yield 'typical' => [
            new RawRangeAggregation(
                self::EXAMPLE_AGGREGATION_NAME,
                self::EXAMPLE_SEARCH_INDEX_FIELD,
                [
                    new Range(null, 10),
                    new Range(10, 100),
                    new Range(100, null),
                ]
            ),
            $emptyLanguageFilter,
            [
                'range' => [
                    'field' => self::EXAMPLE_SEARCH_INDEX_FIELD,
                    'ranges' => [
                        [
                            'to' => 10,
                        ],
                        [
                            'from' => 10,
                            'to' => 100,
                        ],
                        [
                            'from' => 100,
                        ],
                    ],
                ],
            ],
        ];
    }
}

class_alias(RawRangeAggregationVisitorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\AggregationVisitor\RawRangeAggregationVisitorTest');
