<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\ContentService as CoreContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Relation;
use Ibexa\Contracts\Core\Repository\Values\Content\RelationList;
use Ibexa\Contracts\Core\Repository\Values\Content\RelationList\Item\RelationListItem;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Core\Query\QueryFactoryInterface;
use Ibexa\Core\Repository\Values\Content\Location as CoreLocation;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Service\Content\ContentServiceInterface;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;

/**
 * @template TRepositorySudoValues of array
 *
 * @phpstan-type TIsContentIncludedValueMap array<array{\Ibexa\Contracts\Core\Repository\Values\Content\Content, bool }>
 */
abstract class BaseRepositoryEventSubscriberTestCase extends TestCase
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService&\PHPUnit\Framework\MockObject\MockObject */
    protected CoreContentService $coreContentService;

    /** @var \Ibexa\Personalization\Service\Content\ContentServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    protected ContentServiceInterface $contentService;

    /** @var \Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    protected IncludedItemTypeResolverInterface $includedItemTypeResolver;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService&\PHPUnit\Framework\MockObject\MockObject */
    protected LocationService $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\Repository&\PHPUnit\Framework\MockObject\MockObject */
    protected Repository $repository;

    /** @var \Ibexa\Contracts\Core\Repository\SearchService&\PHPUnit\Framework\MockObject\MockObject */
    protected SearchService $searchService;

    /** @var \Ibexa\Core\Query\QueryFactoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    protected QueryFactoryInterface $queryFactory;

    protected function setUp(): void
    {
        $this->coreContentService = $this->createMock(CoreContentService::class);
        $this->contentService = $this->createMock(ContentServiceInterface::class);
        $this->includedItemTypeResolver = $this->createMock(IncludedItemTypeResolverInterface::class);
        $this->locationService = $this->createMock(LocationService::class);
        $this->repository = $this->createMock(Repository::class);
        $this->searchService = $this->createMock(SearchService::class);
        $this->queryFactory = $this->createMock(QueryFactoryInterface::class);
    }

    /**
     * @phpstan-param TRepositorySudoValues $values
     */
    protected function mockRepositorySudo(
        array $values
    ): void {
        $this->repository
            ->expects(self::atLeastOnce())
            ->method('sudo')
            ->with(new IsType(IsType::TYPE_CALLABLE))
            ->willReturnOnConsecutiveCalls(...$values);
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $relatedContentItems
     *
     * @return array{
     *      array{
     *          \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *          bool
     *      }
     * }
     */
    protected function getIncludedRelatedContentItems(array $relatedContentItems): array
    {
        return [
            [$relatedContentItems[0], true],
            [$relatedContentItems[1], true],
        ];
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $childContentItems
     *
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>
     */
    protected function getIncludedChildContentItems(array $childContentItems): array
    {
        return [
            $childContentItems[0],
            $childContentItems[1],
        ];
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $childContentItems
     *
     * @return array{
     *      array{
     *          \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *          bool
     *      }
     * }
     */
    protected function getBaseIsContentIncludedValueMap(
        Content $content,
        array $childContentItems
    ): array {
        return [
            [$content, true],
            [$childContentItems[0], true],
            [$childContentItems[1], true],
            [$childContentItems[2], false],
        ];
    }

    protected function createLocation(Content $content): Location
    {
        return new CoreLocation([
            'id' => 3,
            'content' => $content,
            'contentInfo' => $content->getContentInfo(),
            'pathString' => '/1/2/3/',
        ]);
    }

    protected function createRelationListItemMock(ContentInfo $sourceContentInfo): RelationListItem
    {
        $relation = $this->createMock(Relation::class);
        $relation
            ->method('getSourceContentInfo')
            ->willReturn($sourceContentInfo);

        $relationListItem = $this->createMock(RelationListItem::class);
        $relationListItem
            ->method('hasRelation')
            ->willReturn(true);

        $relationListItem
            ->method('getRelation')
            ->willReturn($relation);

        return $relationListItem;
    }

    protected function createDestinationContentInfoMock(int $id): ContentInfo
    {
        $destinationContentInfo = $this->createMock(ContentInfo::class);
        $destinationContentInfo
            ->method('getId')
            ->willReturn($id);

        return $destinationContentInfo;
    }

    protected function getRelationList(): RelationList
    {
        return new RelationList(
            [
                'items' => [
                    $this->createRelationListItemMock(
                        $this->createDestinationContentInfoMock(1)
                    ),
                    $this->createRelationListItemMock(
                        $this->createDestinationContentInfoMock(2)
                    ),
                ],
            ]
        );
    }

    protected function getLocationSearchResult(Content ...$childContent): SearchResult
    {
        $locations = [];

        foreach ($childContent as $content) {
            $locations[] = $this->createLocation($content);
        }

        return new SearchResult([
            'searchHits' => array_map(
                static fn (Location $location): SearchHit => new SearchHit(['valueObject' => $location]),
                $locations
            ),
            'totalCount' => count($locations),
        ]);
    }

    /**
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>
     */
    protected function getRelatedContentItems(): array
    {
        return [
            $this->createMock(Content::class),
            $this->createMock(Content::class),
        ];
    }

    /**
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>
     */
    protected function getChildContentItems(): array
    {
        return [
            $this->createMock(Content::class),
            $this->createMock(Content::class),
            $this->createMock(Content::class),
        ];
    }

    /**
     * @return array<int>
     */
    protected function getChildCount(): array
    {
        return [
            0,
            0,
            0,
        ];
    }

    protected function mockContentServiceUpdateContent(Content $content): void
    {
        $this->contentService
            ->expects(self::once())
            ->method('updateContent')
            ->with($content);
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $contentItems
     */
    protected function mockContentServiceUpdateContentItems(array $contentItems): void
    {
        if (empty($contentItems)) {
            return;
        }

        $this->contentService
            ->expects(self::once())
            ->method('updateContentItems')
            ->with($contentItems)
            ->willReturnSelf();
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $contentItems
     */
    protected function mockContentServiceDeleteContentItems(array $contentItems): void
    {
        $this->contentService
            ->expects(self::once())
            ->method('deleteContentItems')
            ->with($contentItems);
    }

    /**
     * @phpstan-param TIsContentIncludedValueMap $valueMap
     */
    protected function mockIncludedItemTypeResolverIsContentIncluded(array $valueMap): void
    {
        if (empty($valueMap)) {
            return;
        }

        $this->includedItemTypeResolver
            ->expects(self::atLeastOnce())
            ->method('isContentIncluded')
            ->willReturnMap($valueMap);
    }

    protected function mockLocationSearch(SearchResult $searchResult): void
    {
        $this->queryFactory
            ->method('create')
            ->willReturn(new LocationQuery());

        $this->searchService
            ->method('findLocations')
            ->willReturn($searchResult);
    }
}
