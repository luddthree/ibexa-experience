<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor;

use DateTimeImmutable;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractRangeAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractStatsAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Range;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResultEntry;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\DateRangeAggregationResultExtractor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;

final class DateRangeAggregationResultExtractorTest extends AbstractAggregationResultExtractorTest
{
    protected function createExtractor(): AggregationResultExtractor
    {
        return new DateRangeAggregationResultExtractor(AbstractRangeAggregation::class);
    }

    public function dataProviderForTestSupports(): iterable
    {
        $emptyLanguageFilter = MockUtils::createEmptyLanguageFilter();

        yield 'true' => [
            $this->createMock(AbstractRangeAggregation::class),
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

        yield 'default' => [
            $aggregation,
            $emptyLanguageFilter,
            [
                'buckets' => [],
            ],
            new RangeAggregationResult(self::EXAMPLE_AGGREGATION_NAME, []),
        ];

        yield 'typical' => [
            $aggregation,
            $emptyLanguageFilter,
            [
                'buckets' => [
                    [
                        'from' => null,
                        'from_as_string' => null,
                        'to' => 1517443208000,
                        'to_as_string' => '2018-01-01 00:00:00',
                        'doc_count' => 10,
                    ],
                    [
                        'from' => 1517443208000,
                        'from_as_string' => '2018-01-01 00:00:00',
                        'to' => 1546300800000,
                        'to_as_string' => '2019-01-01 00:00:00',
                        'doc_count' => 100,
                    ],
                    [
                        'from' => 1546300800000,
                        'from_as_string' => '2019-01-01 00:00:00',
                        'to' => null,
                        'to_as_string' => null,
                        'doc_count' => 1000,
                    ],
                ],
            ],
            new RangeAggregationResult(
                self::EXAMPLE_AGGREGATION_NAME,
                [
                    new RangeAggregationResultEntry(
                        new Range(null, new DateTimeImmutable('2018-01-01 00:00:00')),
                        10
                    ),
                    new RangeAggregationResultEntry(
                        new Range(new DateTimeImmutable('2018-01-01 00:00:00'), new DateTimeImmutable('2019-01-01 00:00:00')),
                        100
                    ),
                    new RangeAggregationResultEntry(
                        new Range(new DateTimeImmutable('2019-01-01 00:00:00'), null),
                        1000
                    ),
                ]
            ),
        ];
    }
}

class_alias(DateRangeAggregationResultExtractorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\AggregationResultExtractor\DateRangeAggregationResultExtractorTest');
