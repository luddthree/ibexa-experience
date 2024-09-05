<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\SortClauseVisitor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\Elasticsearch\Query\SortClauseVisitor;
use Ibexa\Elasticsearch\Query\SortClauseVisitor\DispatcherVisitor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DispatcherVisitorTest extends TestCase
{
    private const EXAMPLE_VISITOR_RESULT = [
        'content_id_id' => 'desc',
    ];

    public function testSupportsReturnsTrue(): void
    {
        $sortClause = $this->createMock(SortClause::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new DispatcherVisitor([
            $this->createVisitorMockWithSupports($sortClause, $languageFilter, false),
            $this->createVisitorMockWithSupports($sortClause, $languageFilter, true),
            $this->createVisitorMockWithSupports($sortClause, $languageFilter, false),
        ]);

        $this->assertTrue($dispatcher->supports($sortClause, $languageFilter));
    }

    public function testSupportsReturnsFalse(): void
    {
        $sortClause = $this->createMock(SortClause::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new DispatcherVisitor([
            $this->createVisitorMockWithSupports($sortClause, $languageFilter, false),
            $this->createVisitorMockWithSupports($sortClause, $languageFilter, false),
            $this->createVisitorMockWithSupports($sortClause, $languageFilter, false),
        ]);

        $this->assertFalse($dispatcher->supports($sortClause, $languageFilter));
    }

    public function testVisit(): void
    {
        $sortClause = $this->createMock(SortClause::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $visitorA = $this->createVisitorMockWithSupports($sortClause, $languageFilter, false);
        $visitorB = $this->createVisitorMockWithSupports($sortClause, $languageFilter, true);
        $visitorC = $this->createVisitorMockWithSupports($sortClause, $languageFilter, false);

        $dispatcher = new DispatcherVisitor([$visitorA, $visitorB, $visitorC]);

        $visitorB
            ->method('visit')
            ->with($dispatcher, $sortClause)
            ->willReturn(self::EXAMPLE_VISITOR_RESULT);

        $this->assertEquals(
            self::EXAMPLE_VISITOR_RESULT,
            $dispatcher->visit($dispatcher, $sortClause, $languageFilter)
        );
    }

    public function testVisitThrowsNotImplementedException(): void
    {
        $this->expectException(NotImplementedException::class);
        $this->expectExceptionMessage('No visitor available for: ');

        $sortClause = $this->createMock(SortClause::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new DispatcherVisitor([
            $this->createVisitorMockWithSupports($sortClause, $languageFilter, false),
            $this->createVisitorMockWithSupports($sortClause, $languageFilter, false),
            $this->createVisitorMockWithSupports($sortClause, $languageFilter, false),
        ]);

        $dispatcher->visit($dispatcher, $sortClause, $languageFilter);
    }

    private function createVisitorMockWithSupports(SortClause $criterion, LanguageFilter $languageFilter, bool $supports): MockObject
    {
        $visitor = $this->createMock(SortClauseVisitor::class);
        $visitor->method('supports')->with($criterion, $languageFilter)->willReturn($supports);

        return $visitor;
    }
}

class_alias(DispatcherVisitorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\SortClauseVisitor\DispatcherVisitorTest');
