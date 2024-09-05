<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawStatsAggregation;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Elasticsearch\Query\AggregationVisitor\RawStatsAggregationVisitor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;

final class RawStatsAggregationVisitorTest extends AbstractAggregationVisitorTest
{
    private const EXAMPLE_AGGREGATION_NAME = 'custom_aggregation';

    protected function createVisitor(): AggregationVisitor
    {
        return new RawStatsAggregationVisitor();
    }

    public function dataProviderForTestSupports(): iterable
    {
        $emptyLanguageFilter = MockUtils::createEmptyLanguageFilter();

        yield 'true' => [
            new RawStatsAggregation(
                self::EXAMPLE_AGGREGATION_NAME,
                self::EXAMPLE_SEARCH_INDEX_FIELD
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
            new RawStatsAggregation(
                self::EXAMPLE_AGGREGATION_NAME,
                self::EXAMPLE_SEARCH_INDEX_FIELD
            ),
            $emptyLanguageFilter,
            [
                'stats' => [
                    'field' => self::EXAMPLE_SEARCH_INDEX_FIELD,
                ],
            ],
        ];
    }
}

class_alias(RawStatsAggregationVisitorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\AggregationVisitor\RawStatsAggregationVisitorTest');
