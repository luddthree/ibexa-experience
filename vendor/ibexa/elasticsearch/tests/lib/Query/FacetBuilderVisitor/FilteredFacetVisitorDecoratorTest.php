<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\FacetBuilderVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\FacetBuilderVisitor;
use Ibexa\Elasticsearch\Query\FacetBuilderVisitor\FilteredFacetVisitorDecorator;
use Ibexa\Tests\Elasticsearch\Query\ResultExtractor\FacetResultExtractor\TestFacetBuilder;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\TestCase;

final class FilteredFacetVisitorDecoratorTest extends TestCase
{
    private const EXAMPLE_UNWRAPPED_AGGREGATION = [
        'terms' => [
            'foo' => 'bar',
        ],
    ];

    private const EXAMPLE_FILTER = [
        'terms' => [
            'section_id_id' => 2,
        ],
    ];

    /** @var \Ibexa\Contracts\Elasticsearch\Query\FacetBuilderVisitor|\PHPUnit\Framework\MockObject\MockObject */
    private $innerVisitor;

    /** @var \Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor|\PHPUnit\Framework\MockObject\MockObject */
    private $criterionVisitor;

    /** @var \Ibexa\Elasticsearch\Query\FacetBuilderVisitor\FilteredFacetVisitorDecorator */
    private $visitor;

    protected function setUp(): void
    {
        $this->innerVisitor = $this->createMock(FacetBuilderVisitor::class);
        $this->criterionVisitor = $this->createMock(CriterionVisitor::class);
        $this->visitor = new FilteredFacetVisitorDecorator($this->innerVisitor, $this->criterionVisitor);
    }

    public function testSupports(): void
    {
        $args = [
            new TestFacetBuilder(),
            MockUtils::createEmptyLanguageFilter(),
        ];

        $this->innerVisitor
            ->expects($this->once())
            ->method('supports')
            ->with(...$args)
            ->willReturn(true);

        $this->assertTrue($this->visitor->supports(...$args));
    }

    public function testVisitFilteredFacet(): void
    {
        $criteria = $this->createMock(Criterion::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $args = [
            $this->createMock(FacetBuilderVisitor::class),
            new TestFacetBuilder([
                'filter' => $criteria,
            ]),
            $languageFilter,
        ];

        $this->innerVisitor
            ->expects($this->once())
            ->method('visit')
            ->with(...$args)
            ->willReturn(self::EXAMPLE_UNWRAPPED_AGGREGATION);

        $this->criterionVisitor
            ->method('visit')
            ->with($this->criterionVisitor, $criteria, $languageFilter)
            ->willReturn(self::EXAMPLE_FILTER);

        $this->assertEquals(
            [
                'filter' => self::EXAMPLE_FILTER,
                'aggs' => [
                    FilteredFacetVisitorDecorator::INNER_AGGREGATION_KEY => self::EXAMPLE_UNWRAPPED_AGGREGATION,
                ],
            ],
            $this->visitor->visit(...$args)
        );
    }

    public function testVisitNonFilteredFacet(): void
    {
        $args = [
            $this->createMock(FacetBuilderVisitor::class),
            new TestFacetBuilder([
                'filter' => null,
            ]),
            MockUtils::createEmptyLanguageFilter(),
        ];

        $this->innerVisitor
            ->expects($this->once())
            ->method('visit')
            ->with(...$args)
            ->willReturn(self::EXAMPLE_UNWRAPPED_AGGREGATION);

        $this->assertEquals(
            self::EXAMPLE_UNWRAPPED_AGGREGATION,
            $this->visitor->visit(...$args)
        );
    }
}

class_alias(FilteredFacetVisitorDecoratorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\FacetBuilderVisitor\FilteredFacetVisitorDecoratorTest');
