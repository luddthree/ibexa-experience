<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\NestedAggregationResultExtractor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\TestCase;

final class NestedAggregationResultExtractorTest extends TestCase
{
    private const EXAMPLE_NESTED_RESULT_KEY = 'foo';
    private const EXAMPLE_UNWRAPPED_DATA = [
        'buckets' => [
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
        ],
    ];

    /** @var \Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor|\PHPUnit\Framework\MockObject\MockObject */
    private $innerResultExtractor;

    /** @var \Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\NestedAggregationResultExtractor */
    private $resultExtractor;

    protected function setUp(): void
    {
        $this->innerResultExtractor = $this->createMock(AggregationResultExtractor::class);
        $this->resultExtractor = new NestedAggregationResultExtractor(
            $this->innerResultExtractor,
            self::EXAMPLE_NESTED_RESULT_KEY
        );
    }

    public function testSupports(): void
    {
        $aggregation = $this->createMock(Aggregation::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $this->innerResultExtractor
            ->expects($this->once())
            ->method('supports')
            ->with($aggregation, $languageFilter)
            ->willReturn(true);

        $this->assertTrue($this->resultExtractor->supports($aggregation, $languageFilter));
    }

    public function testExtract(): void
    {
        $expectedResult = $this->createMock(AggregationResult::class);

        $aggregation = $this->createMock(Aggregation::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $this->innerResultExtractor
            ->expects($this->once())
            ->method('extract')
            ->with($aggregation, $languageFilter, self::EXAMPLE_UNWRAPPED_DATA)
            ->willReturn($expectedResult);

        $wrappedData = [
            self::EXAMPLE_NESTED_RESULT_KEY => self::EXAMPLE_UNWRAPPED_DATA,
        ];

        $this->assertEquals(
            $expectedResult,
            $this->resultExtractor->extract(
                $aggregation,
                $languageFilter,
                $wrappedData
            )
        );
    }
}

class_alias(NestedAggregationResultExtractorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\AggregationResultExtractor\NestedAggregationResultExtractorTest');
