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
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use PHPUnit\Framework\TestCase;

abstract class AbstractAggregationResultExtractorTest extends TestCase
{
    protected const EXAMPLE_AGGREGATION_NAME = 'custom_aggregation';

    /** @var \Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor */
    protected $extractor;

    protected function setUp(): void
    {
        $this->extractor = $this->createExtractor();
    }

    abstract protected function createExtractor(): AggregationResultExtractor;

    /**
     * @dataProvider dataProviderForTestSupports
     */
    public function testSupports(
        Aggregation $aggregation,
        LanguageFilter $languageFilter,
        bool $expectedResult
    ): void {
        $this->assertEquals(
            $expectedResult,
            $this->extractor->supports($aggregation, $languageFilter)
        );
    }

    abstract public function dataProviderForTestSupports(): iterable;

    /**
     * @dataProvider dataProviderForTestExtract
     */
    public function testExtract(
        Aggregation $aggregation,
        LanguageFilter $languageFilter,
        array $rawData,
        AggregationResult $expectedResult
    ): void {
        $this->configureMocksForTestExtract($aggregation, $languageFilter, $rawData, $expectedResult);

        $this->assertEquals(
            $expectedResult,
            $this->extractor->extract($aggregation, $languageFilter, $rawData)
        );
    }

    abstract public function dataProviderForTestExtract(): iterable;

    protected function configureMocksForTestExtract(
        Aggregation $aggregation,
        LanguageFilter $languageFilter,
        array $rawData,
        AggregationResult $expectedResult
    ): void {
    }
}

class_alias(AbstractAggregationResultExtractorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\AggregationResultExtractor\AbstractAggregationResultExtractorTest');
