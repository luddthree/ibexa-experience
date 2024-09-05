<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractRangeAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Range;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResultEntry;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\RangeAggregationResultExtractor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;

final class RangeAggregationResultExtractorTest extends AbstractAggregationResultExtractorTest
{
    protected function createExtractor(): AggregationResultExtractor
    {
        return new RangeAggregationResultExtractor(AbstractRangeAggregation::class);
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

        $aggregation = $this->createMock(AbstractRangeAggregation::class);
        $aggregation->method('getName')->willReturn(self::EXAMPLE_AGGREGATION_NAME);
        $aggregation->method('getRanges')->willReturn([
            new Range(Range::INF, 10, 'a'),
            new Range(10, 100, 'b'),
            new Range(100, Range::INF, 'c'),
        ]);

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
                        'to' => 10,
                        'doc_count' => 10,
                    ],
                    [
                        'from' => 10,
                        'to' => 100,
                        'doc_count' => 100,
                    ],
                    [
                        'from' => 100,
                        'to' => null,
                        'doc_count' => 1000,
                    ],
                ],
            ],
            new RangeAggregationResult(
                self::EXAMPLE_AGGREGATION_NAME,
                [
                    new RangeAggregationResultEntry(new Range(Range::INF, '10', 'a'), 10),
                    new RangeAggregationResultEntry(new Range('10', '100', 'b'), 100),
                    new RangeAggregationResultEntry(new Range('100', Range::INF, 'c'), 1000),
                ]
            ),
        ];
    }
}

class_alias(RangeAggregationResultExtractorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\AggregationResultExtractor\RangeAggregationResultExtractorTest');
