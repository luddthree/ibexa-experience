<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\DispatcherAggregationResultExtractor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\TestCase;

final class DispatcherAggregationResultExtractorTest extends TestCase
{
    public function testSupportsReturnsTrue(): void
    {
        $aggregation = $this->createMock(Aggregation::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new DispatcherAggregationResultExtractor([
            $this->createExtractorMockWithSupports($aggregation, $languageFilter, false),
            $this->createExtractorMockWithSupports($aggregation, $languageFilter, true),
            $this->createExtractorMockWithSupports($aggregation, $languageFilter, false),
        ]);

        $this->assertTrue($dispatcher->supports($aggregation, $languageFilter));
    }

    public function testSupportsReturnsFalse(): void
    {
        $aggregation = $this->createMock(Aggregation::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new DispatcherAggregationResultExtractor([
            $this->createExtractorMockWithSupports($aggregation, $languageFilter, false),
            $this->createExtractorMockWithSupports($aggregation, $languageFilter, false),
            $this->createExtractorMockWithSupports($aggregation, $languageFilter, false),
        ]);

        $this->assertFalse($dispatcher->supports($aggregation, $languageFilter));
    }

    public function testExtract(): void
    {
        $aggregation = $this->createMock(Aggregation::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();
        $data = [];

        $extractorA = $this->createExtractorMockWithSupports($aggregation, $languageFilter, false);
        $extractorB = $this->createExtractorMockWithSupports($aggregation, $languageFilter, true);
        $extractorC = $this->createExtractorMockWithSupports($aggregation, $languageFilter, false);

        $dispatcher = new DispatcherAggregationResultExtractor([$extractorA, $extractorB, $extractorC]);

        $expectedResult = $this->createMock(AggregationResult::class);

        $extractorB
            ->method('extract')
            ->with($aggregation, $languageFilter, $data)
            ->willReturn($expectedResult);

        $this->assertEquals(
            $expectedResult,
            $dispatcher->extract($aggregation, $languageFilter, $data)
        );
    }

    public function testVisitThrowsNotImplementedException(): void
    {
        $this->expectException(NotImplementedException::class);
        $this->expectExceptionMessage('No result extractor available for aggregation: ');

        $aggregation = $this->createMock(Aggregation::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new DispatcherAggregationResultExtractor([
            $this->createExtractorMockWithSupports($aggregation, $languageFilter, false),
            $this->createExtractorMockWithSupports($aggregation, $languageFilter, false),
            $this->createExtractorMockWithSupports($aggregation, $languageFilter, false),
        ]);

        $dispatcher->extract($aggregation, $languageFilter, [/* data is not important for test case */]);
    }

    private function createExtractorMockWithSupports(
        Aggregation $aggregation,
        LanguageFilter $languageFilter,
        bool $supports
    ): AggregationResultExtractor {
        $extractor = $this->createMock(AggregationResultExtractor::class);
        $extractor->method('supports')->with($aggregation, $languageFilter)->willReturn($supports);

        return $extractor;
    }
}

class_alias(DispatcherAggregationResultExtractorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\AggregationResultExtractor\DispatcherAggregationResultExtractorTest');
