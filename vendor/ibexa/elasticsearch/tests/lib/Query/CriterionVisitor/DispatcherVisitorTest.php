<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\CriterionVisitor\DispatcherVisitor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DispatcherVisitorTest extends TestCase
{
    private const EXAMPLE_VISITOR_RESULT = [
        'term' => [
            'content_id_id' => 54,
        ],
    ];

    public function testSupportsReturnsTrue(): void
    {
        $criterion = $this->createMock(Criterion::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new DispatcherVisitor([
            $this->createVisitorMockWithSupports($criterion, $languageFilter, false),
            $this->createVisitorMockWithSupports($criterion, $languageFilter, true),
            $this->createVisitorMockWithSupports($criterion, $languageFilter, false),
        ]);

        $this->assertTrue($dispatcher->supports($criterion, $languageFilter));
    }

    public function testSupportsReturnsFalse(): void
    {
        $criterion = $this->createMock(Criterion::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new DispatcherVisitor([
            $this->createVisitorMockWithSupports($criterion, $languageFilter, false),
            $this->createVisitorMockWithSupports($criterion, $languageFilter, false),
            $this->createVisitorMockWithSupports($criterion, $languageFilter, false),
        ]);

        $this->assertFalse($dispatcher->supports($criterion, $languageFilter));
    }

    public function testVisit(): void
    {
        $criterion = $this->createMock(Criterion::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $visitorA = $this->createVisitorMockWithSupports($criterion, $languageFilter, false);
        $visitorB = $this->createVisitorMockWithSupports($criterion, $languageFilter, true);
        $visitorC = $this->createVisitorMockWithSupports($criterion, $languageFilter, false);

        $dispatcher = new DispatcherVisitor([$visitorA, $visitorB, $visitorC]);

        $visitorB
            ->method('visit')
            ->with($dispatcher, $criterion, $languageFilter)
            ->willReturn(self::EXAMPLE_VISITOR_RESULT);

        $this->assertEquals(
            self::EXAMPLE_VISITOR_RESULT,
            $dispatcher->visit($dispatcher, $criterion, $languageFilter)
        );
    }

    public function testVisitThrowsNotImplementedException(): void
    {
        $this->expectException(NotImplementedException::class);
        $this->expectExceptionMessage('No visitor available for: ');

        $criterion = $this->createMock(Criterion::class);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $dispatcher = new DispatcherVisitor([
            $this->createVisitorMockWithSupports($criterion, $languageFilter, false),
            $this->createVisitorMockWithSupports($criterion, $languageFilter, false),
            $this->createVisitorMockWithSupports($criterion, $languageFilter, false),
        ]);

        $dispatcher->visit($dispatcher, $criterion, $languageFilter);
    }

    private function createVisitorMockWithSupports(
        Criterion $criterion,
        LanguageFilter $languageFilter,
        bool $supports
    ): MockObject {
        $visitor = $this->createMock(CriterionVisitor::class);
        $visitor->method('supports')->with($criterion, $languageFilter)->willReturn($supports);

        return $visitor;
    }
}

class_alias(DispatcherVisitorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\CriterionVisitor\DispatcherVisitorTest');
