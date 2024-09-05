<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\ContentTypeGroupAggregationKeyMapper;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\TestCase;

final class ContentTypeGroupAggregationKeyMapperTest extends TestCase
{
    private const EXAMPLE_CONTENT_TYPE_GROUPS_IDS = ['1', '2', '3'];

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $contentTypeService;

    protected function setUp(): void
    {
        $this->contentTypeService = $this->createMock(ContentTypeService::class);
    }

    public function testMap(): void
    {
        $expectedContentTypesGroups = $this->configureContentTypeServiceMock();

        $mapper = new ContentTypeGroupAggregationKeyMapper($this->contentTypeService);

        $this->assertEquals(
            $expectedContentTypesGroups,
            $mapper->map(
                $this->createMock(Aggregation::class),
                MockUtils::createEmptyLanguageFilter(),
                self::EXAMPLE_CONTENT_TYPE_GROUPS_IDS
            )
        );
    }

    private function createContentTypeGroupsWithIds(int $id): ContentTypeGroup
    {
        $contentTypeGroup = $this->createMock(ContentTypeGroup::class);
        $contentTypeGroup->method('__get')->with('id')->willReturn($id);

        return $contentTypeGroup;
    }

    private function configureContentTypeServiceMock(): array
    {
        $expectedContentTypesGroups = [];

        foreach (self::EXAMPLE_CONTENT_TYPE_GROUPS_IDS as $i => $id) {
            $contentTypeGroup = $this->createContentTypeGroupsWithIds((int)$id);

            $this->contentTypeService
                ->expects($this->at($i))
                ->method('loadContentTypeGroup')
                ->with((int)$id, [])
                ->willReturn($contentTypeGroup);

            $expectedContentTypesGroups[$id] = $contentTypeGroup;
        }

        return $expectedContentTypesGroups;
    }
}

class_alias(ContentTypeGroupAggregationKeyMapperTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\ContentTypeGroupAggregationKeyMapperTest');
