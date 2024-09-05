<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractStatsAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\StatsAggregationResult;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\StatsAggregationResultExtractor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;

final class StatsAggregationResultExtractorTest extends AbstractAggregationResultExtractorTest
{
    protected function createExtractor(): AggregationResultExtractor
    {
        return new StatsAggregationResultExtractor(AbstractStatsAggregation::class);
    }

    public function dataProviderForTestSupports(): iterable
    {
        $emptyLanguageFilter = MockUtils::createEmptyLanguageFilter();

        yield 'true' => [
            $this->createMock(AbstractStatsAggregation::class),
            $emptyLanguageFilter,
            true,
        ];

        yield 'false' => [
            $this->createMock(Aggregation::class),
            $emptyLanguageFilter,
            false,
        ];
    }

    public function dataProviderForTestExtract(): iterable
    {
        $emptyLanguageFilter = MockUtils::createEmptyLanguageFilter();

        $aggregation = $this->createMock(AbstractStatsAggregation::class);
        $aggregation->method('getName')->willReturn(self::EXAMPLE_AGGREGATION_NAME);

        yield 'defaults' => [
            $aggregation,
            $emptyLanguageFilter,
            [],
            new StatsAggregationResult(
                self::EXAMPLE_AGGREGATION_NAME,
                null,
                null,
                null,
                null,
                null,
            ),
        ];

        yield 'typical' => [
            $aggregation,
            $emptyLanguageFilter,
            [
                'count' => 1000,
                'min' => 0,
                'max' => 10.0,
                'avg' => 100.0,
                'sum' => 1000.0,
            ],
            new StatsAggregationResult(
                self::EXAMPLE_AGGREGATION_NAME,
                1000,
                0,
                10.0,
                100.0,
                1000.0
            ),
        ];
    }
}

class_alias(StatsAggregationResultExtractorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\AggregationResultExtractor\StatsAggregationResultExtractorTest');
