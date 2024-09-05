<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractTermAggregation;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver;
use Ibexa\Elasticsearch\Query\AggregationVisitor\TermAggregationVisitor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;

final class TermAggregationVisitorTest extends AbstractAggregationVisitorTest
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
            $this->createMock(AbstractTermAggregation::class),
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
            $this->createMock(AbstractTermAggregation::class),
            $emptyLanguageFilter,
            [
                'terms' => [
                    'field' => self::EXAMPLE_SEARCH_INDEX_FIELD,
                    'size' => 0,
                    'min_doc_count' => 0,
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
        return new TermAggregationVisitor(AbstractTermAggregation::class, $this->fieldResolver);
    }
}

class_alias(TermAggregationVisitorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\AggregationVisitor\TermAggregationVisitorTest');
