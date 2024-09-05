<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use PHPUnit\Framework\TestCase;

abstract class AbstractAggregationVisitorTest extends TestCase
{
    protected const EXAMPLE_SEARCH_INDEX_FIELD = 'custom_field_id';

    /** @var \Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor */
    protected $visitor;

    /** @var \Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor|\PHPUnit\Framework\MockObject\MockObject */
    protected $dispatcherVisitor;

    protected function setUp(): void
    {
        $this->visitor = $this->createVisitor();
        $this->dispatcherVisitor = $this->createMock(AggregationVisitor::class);
    }

    abstract protected function createVisitor(): AggregationVisitor;

    /**
     * @dataProvider dataProviderForTestSupports
     */
    final public function testSupports(
        Aggregation $aggregation,
        LanguageFilter $languageFilter,
        bool $expectedValue
    ): void {
        $this->assertEquals(
            $expectedValue,
            $this->visitor->supports($aggregation, $languageFilter)
        );
    }

    abstract public function dataProviderForTestSupports(): iterable;

    /**
     * @dataProvider dataProviderForTestVisit
     */
    final public function testVisit(
        Aggregation $aggregation,
        LanguageFilter $languageFilter,
        array $expectedResult
    ): void {
        $this->configureMocksForTestVisit($aggregation, $languageFilter, $expectedResult);

        $this->assertEquals(
            $expectedResult,
            $this->visitor->visit($this->dispatcherVisitor, $aggregation, $languageFilter)
        );
    }

    abstract public function dataProviderForTestVisit(): iterable;

    protected function configureMocksForTestVisit(
        Aggregation $aggregation,
        LanguageFilter $languageFilter,
        array $expectedResult
    ): void {
        // Overwrite in parent class to configure additional mocks
    }
}

class_alias(AbstractAggregationVisitorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\AggregationVisitor\AbstractAggregationVisitorTest');
