<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\SectionAggregationKeyMapper;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\TestCase;

final class SectionAggregationKeyMapperTest extends TestCase
{
    private const EXAMPLE_SECTION_IDS = ['1', '2', '3'];

    /** @var \Ibexa\Contracts\Core\Repository\SectionService|\PHPUnit\Framework\MockObject\MockObject */
    private $sectionService;

    protected function setUp(): void
    {
        $this->sectionService = $this->createMock(SectionService::class);
    }

    public function testMap(): void
    {
        $expectedResult = $this->configureSectionServiceMock(
            self::EXAMPLE_SECTION_IDS
        );

        $mapper = new SectionAggregationKeyMapper($this->sectionService);

        $this->assertEquals(
            $expectedResult,
            $mapper->map(
                $this->createMock(Aggregation::class),
                MockUtils::createEmptyLanguageFilter(),
                self::EXAMPLE_SECTION_IDS
            )
        );
    }

    private function createSectionWithId(int $id): Section
    {
        $section = $this->createMock(Section::class);
        $section->method('__get')->with('id')->willReturn($id);

        return $section;
    }

    private function configureSectionServiceMock(iterable $ids): array
    {
        $expectedSections = [];
        foreach ($ids as $i => $id) {
            $section = $this->createSectionWithId((int)$id);

            $this->sectionService
                ->expects($this->at($i))
                ->method('loadSection')
                ->with((int)$id)
                ->willReturn($section);

            $expectedSections[$id] = $section;
        }

        return $expectedSections;
    }
}

class_alias(SectionAggregationKeyMapperTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\SectionAggregationKeyMapperTest');
