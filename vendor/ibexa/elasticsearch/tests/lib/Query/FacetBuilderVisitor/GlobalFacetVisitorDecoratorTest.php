<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\FacetBuilderVisitor;

use Ibexa\Contracts\Elasticsearch\Query\FacetBuilderVisitor;
use Ibexa\Elasticsearch\Query\FacetBuilderVisitor\GlobalFacetVisitorDecorator;
use Ibexa\Tests\Elasticsearch\Query\ResultExtractor\FacetResultExtractor\TestFacetBuilder;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\TestCase;
use stdClass;

final class GlobalFacetVisitorDecoratorTest extends TestCase
{
    private const EXAMPLE_UNWRAPPED_AGGREGATION = [
        'terms' => [
            'foo' => 'bar',
        ],
    ];

    /** @var \Ibexa\Contracts\Elasticsearch\Query\FacetBuilderVisitor|\PHPUnit\Framework\MockObject\MockObject */
    private $innerVisitor;

    /** @var \Ibexa\Elasticsearch\Query\FacetBuilderVisitor\GlobalFacetVisitorDecorator */
    private $visitor;

    protected function setUp(): void
    {
        $this->innerVisitor = $this->createMock(FacetBuilderVisitor::class);
        $this->visitor = new GlobalFacetVisitorDecorator($this->innerVisitor);
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

    public function testVisitGlobalFacet(): void
    {
        $args = [
            $this->createMock(FacetBuilderVisitor::class),
            new TestFacetBuilder([
                'global' => true,
            ]),
            MockUtils::createEmptyLanguageFilter(),
        ];

        $this->innerVisitor
            ->expects($this->once())
            ->method('visit')
            ->with(...$args)
            ->willReturn(self::EXAMPLE_UNWRAPPED_AGGREGATION);

        $this->assertEquals(
            [
                'global' => new stdClass(),
                'aggs' => [
                    GlobalFacetVisitorDecorator::INNER_AGGREGATION_KEY => self::EXAMPLE_UNWRAPPED_AGGREGATION,
                ],
            ],
            $this->visitor->visit(...$args)
        );
    }

    public function testVisitNonGlobalFacet(): void
    {
        $args = [
            $this->createMock(FacetBuilderVisitor::class),
            new TestFacetBuilder([
                'global' => false,
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

class_alias(GlobalFacetVisitorDecoratorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\FacetBuilderVisitor\GlobalFacetVisitorDecoratorTest');
