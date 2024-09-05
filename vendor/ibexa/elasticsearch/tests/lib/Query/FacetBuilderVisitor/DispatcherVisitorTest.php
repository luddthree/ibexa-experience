<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\FacetBuilderVisitor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;
use Ibexa\Contracts\Elasticsearch\Query\FacetBuilderVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\FacetBuilderVisitor\DispatcherVisitor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DispatcherVisitorTest extends TestCase
{
    private const EXAMPLE_VISITOR_RESULT = [
        'terms' => [
            'field' => 'foo',
            'size' => 10,
        ],
    ];

    public function testSupportsReturnsTrue(): void
    {
        $facetBuilder = $this->createMock(FacetBuilder::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new DispatcherVisitor([
            $this->createVisitorMockWithSupports($facetBuilder, $languageFilter, false),
            $this->createVisitorMockWithSupports($facetBuilder, $languageFilter, true),
            $this->createVisitorMockWithSupports($facetBuilder, $languageFilter, false),
        ]);

        $this->assertTrue($dispatcher->supports($facetBuilder, $languageFilter));
    }

    public function testSupportsReturnsFalse(): void
    {
        $facetBuilder = $this->createMock(FacetBuilder::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new DispatcherVisitor([
            $this->createVisitorMockWithSupports($facetBuilder, $languageFilter, false),
            $this->createVisitorMockWithSupports($facetBuilder, $languageFilter, false),
            $this->createVisitorMockWithSupports($facetBuilder, $languageFilter, false),
        ]);

        $this->assertFalse($dispatcher->supports($facetBuilder, $languageFilter));
    }

    public function testVisit(): void
    {
        $facetBuilder = $this->createMock(FacetBuilder::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $visitorA = $this->createVisitorMockWithSupports($facetBuilder, $languageFilter, false);
        $visitorB = $this->createVisitorMockWithSupports($facetBuilder, $languageFilter, true);
        $visitorC = $this->createVisitorMockWithSupports($facetBuilder, $languageFilter, false);

        $dispatcher = new DispatcherVisitor([$visitorA, $visitorB, $visitorC]);

        $visitorB
            ->method('visit')
            ->with($dispatcher, $facetBuilder, $languageFilter)
            ->willReturn(self::EXAMPLE_VISITOR_RESULT);

        $this->assertEquals(
            self::EXAMPLE_VISITOR_RESULT,
            $dispatcher->visit($dispatcher, $facetBuilder, $languageFilter)
        );
    }

    public function testVisitThrowsNotImplementedException(): void
    {
        $this->expectException(NotImplementedException::class);
        $this->expectExceptionMessage('No visitor available for: ');

        $facetBuilder = $this->createMock(FacetBuilder::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new DispatcherVisitor([
            $this->createVisitorMockWithSupports($facetBuilder, $languageFilter, false),
            $this->createVisitorMockWithSupports($facetBuilder, $languageFilter, false),
            $this->createVisitorMockWithSupports($facetBuilder, $languageFilter, false),
        ]);

        $dispatcher->visit($dispatcher, $facetBuilder, $languageFilter);
    }

    private function createVisitorMockWithSupports(
        FacetBuilder $facetBuilder,
        LanguageFilter $languageFilter,
        bool $supports
    ): MockObject {
        $visitor = $this->createMock(FacetBuilderVisitor::class);
        $visitor->method('supports')->with($facetBuilder, $languageFilter)->willReturn($supports);

        return $visitor;
    }
}

class_alias(DispatcherVisitorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\FacetBuilderVisitor\DispatcherVisitorTest');
