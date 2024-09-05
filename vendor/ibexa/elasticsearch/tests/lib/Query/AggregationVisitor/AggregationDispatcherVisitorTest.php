<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationDispatcherVisitor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class AggregationDispatcherVisitorTest extends TestCase
{
    private const EXAMPLE_VISITOR_RESULT = [
        'terms' => [
            'field' => 'foo',
            'size' => 10,
        ],
    ];

    public function testSupportsReturnsTrue(): void
    {
        $aggregation = $this->createMock(Aggregation::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new AggregationDispatcherVisitor([
            $this->createVisitorMockWithSupports($aggregation, $languageFilter, false),
            $this->createVisitorMockWithSupports($aggregation, $languageFilter, true),
            $this->createVisitorMockWithSupports($aggregation, $languageFilter, false),
        ]);

        $this->assertTrue($dispatcher->supports($aggregation, $languageFilter));
    }

    public function testSupportsReturnsFalse(): void
    {
        $aggregation = $this->createMock(Aggregation::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new AggregationDispatcherVisitor([
            $this->createVisitorMockWithSupports($aggregation, $languageFilter, false),
            $this->createVisitorMockWithSupports($aggregation, $languageFilter, false),
            $this->createVisitorMockWithSupports($aggregation, $languageFilter, false),
        ]);

        $this->assertFalse($dispatcher->supports($aggregation, $languageFilter));
    }

    public function testVisit(): void
    {
        $aggregation = $this->createMock(Aggregation::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $visitorA = $this->createVisitorMockWithSupports($aggregation, $languageFilter, false);
        $visitorB = $this->createVisitorMockWithSupports($aggregation, $languageFilter, true);
        $visitorC = $this->createVisitorMockWithSupports($aggregation, $languageFilter, false);

        $dispatcher = new AggregationDispatcherVisitor([$visitorA, $visitorB, $visitorC]);

        $visitorB
            ->method('visit')
            ->with($dispatcher, $aggregation, $languageFilter)
            ->willReturn(self::EXAMPLE_VISITOR_RESULT);

        $this->assertEquals(
            self::EXAMPLE_VISITOR_RESULT,
            $dispatcher->visit($dispatcher, $aggregation, $languageFilter)
        );
    }

    public function testVisitThrowsNotImplementedException(): void
    {
        $this->expectException(NotImplementedException::class);
        $this->expectExceptionMessage('No visitor available for: ');

        $aggregation = $this->createMock(Aggregation::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new AggregationDispatcherVisitor([
            $this->createVisitorMockWithSupports($aggregation, $languageFilter, false),
            $this->createVisitorMockWithSupports($aggregation, $languageFilter, false),
            $this->createVisitorMockWithSupports($aggregation, $languageFilter, false),
        ]);

        $dispatcher->visit($dispatcher, $aggregation, $languageFilter);
    }

    private function createVisitorMockWithSupports(
        Aggregation $aggregation,
        LanguageFilter $languageFilter,
        bool $supports
    ): MockObject {
        $visitor = $this->createMock(AggregationVisitor::class);
        $visitor->method('supports')->with($aggregation, $languageFilter)->willReturn($supports);

        return $visitor;
    }
}

class_alias(AggregationDispatcherVisitorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\AggregationVisitor\AggregationDispatcherVisitorTest');
