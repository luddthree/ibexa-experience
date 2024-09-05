<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractRangeAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Range;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver;
use Ibexa\Elasticsearch\Query\AggregationVisitor\RangeAggregationVisitor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;

final class RangeAggregationVisitorTest extends AbstractAggregationVisitorTest
{
    /** @var \Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $fieldResolver;

    protected function setUp(): void
    {
        $this->fieldResolver = $this->createMock(AggregationFieldResolver::class);

        parent::setUp();
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

    public function dataProviderForTestVisit(): iterable
    {
        $emptyLanguageFilter = MockUtils::createEmptyLanguageFilter();

        $aggregation = $this->createMock(AbstractRangeAggregation::class);
        $aggregation
            ->method('getRanges')
            ->willReturn([
                new Range(null, 10),
                new Range(10, 100),
                new Range(100, null),
            ]);

        yield 'typical' => [
            $aggregation,
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

    protected function configureMocksForTestVisit(
        Aggregation $aggregation,
        LanguageFilter $languageFilter,
        array $expectedResult
    ): void {
        $this->fieldResolver
            ->method('resolveTargetField')
            ->with($aggregation)
            ->willReturn(self::EXAMPLE_SEARCH_INDEX_FIELD);
    }

    protected function createVisitor(): AggregationVisitor
    {
        return new RangeAggregationVisitor(AbstractRangeAggregation::class, $this->fieldResolver);
    }
}

class_alias(RangeAggregationVisitorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\AggregationVisitor\RangeAggregationVisitorTest');
