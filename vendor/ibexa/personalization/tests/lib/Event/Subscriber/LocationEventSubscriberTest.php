<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Events\Location\CopySubtreeEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\CreateLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\HideLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\MoveSubtreeEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\SwapLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\UnhideLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\UpdateLocationEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Core\Repository\Values\Content\Location as CoreLocation;
use Ibexa\Personalization\Event\Subscriber\LocationEventSubscriber;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @covers \Ibexa\Personalization\Event\Subscriber\LocationEventSubscriber
 *
 * @phpstan-type T array<object|int|array<object>>
 * @phpstan-extends \Ibexa\Tests\Personalization\Event\Subscriber\BaseRepositoryEventSubscriberTestCase<T>
 *
 * @phpstan-import-type TIsContentIncludedValueMap from \Ibexa\Tests\Personalization\Event\Subscriber\BaseRepositoryEventSubscriberTestCase
 */
final class LocationEventSubscriberTest extends BaseRepositoryEventSubscriberTestCase
{
    /** @var \Symfony\Component\EventDispatcher\EventSubscriberInterface&\Ibexa\Personalization\Event\Subscriber\LocationEventSubscriber */
    private EventSubscriberInterface $locationEventSubscriber;

    public function setUp(): void
    {
        parent::setUp();

        $settingService = $this->createMock(SettingServiceInterface::class);
        $settingService->method('isInstallationKeyFound')->willReturn(true);
        $this->locationEventSubscriber = new LocationEventSubscriber(
            $this->coreContentService,
            $this->contentService,
            $this->includedItemTypeResolver,
            $this->locationService,
            $this->repository,
            $settingService,
            $this->queryFactory,
            $this->searchService
        );
    }

    /**
     * @dataProvider provideDataLocationWithChildren

     * @phpstan-param TIsContentIncludedValueMap $includedItemTypeResolverMockValueMap
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $contentItemsToUpdate
     */
    public function testOnCopySubtree(
        Location $location,
        SearchResult $locationSearchResult,
        array $includedItemTypeResolverMockValueMap,
        array $contentItemsToUpdate
    ): void {
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockContentServiceUpdateContentItems($contentItemsToUpdate);
        $this->mockLocationSearch($locationSearchResult);

        $this->locationEventSubscriber->onCopySubtree(
            new CopySubtreeEvent(
                $location,
                $this->createMock(Location::class),
                $this->createMock(Location::class)
            )
        );
    }

    /**
     * @dataProvider provideDataLocationWithChildren
     *
     * @phpstan-param TIsContentIncludedValueMap $includedItemTypeResolverMockValueMap
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $contentItemsToUpdate
     */
    public function testOnMoveSubtree(
        Location $location,
        SearchResult $locationSearchResult,
        array $includedItemTypeResolverMockValueMap,
        array $contentItemsToUpdate
    ): void {
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockContentServiceUpdateContentItems($contentItemsToUpdate);
        $this->mockLocationSearch($locationSearchResult);

        $this->locationEventSubscriber->onMoveSubtree(
            new MoveSubtreeEvent(
                $location,
                $this->createMock(Location::class)
            )
        );
    }

    /**
     * @dataProvider provideDataLocationWithChildren
     *
     * @phpstan-param TIsContentIncludedValueMap $includedItemTypeResolverMockValueMap
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $contentItemsToUpdate
     */
    public function testOnUnhideLocation(
        Location $location,
        SearchResult $locationSearchResult,
        array $includedItemTypeResolverMockValueMap,
        array $contentItemsToUpdate
    ): void {
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockContentServiceUpdateContentItems($contentItemsToUpdate);
        $this->mockLocationSearch($locationSearchResult);

        $this->locationEventSubscriber->onUnhideLocation(
            new UnhideLocationEvent(
                $this->createMock(Location::class),
                $location
            )
        );
    }

    /**
     * @dataProvider provideDataLocationWithChildren
     *
     * @phpstan-param TIsContentIncludedValueMap $includedItemTypeResolverMockValueMap
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $contentItemsToUpdate
     */
    public function testOnUpdateLocation(
        Location $location,
        SearchResult $locationSearchResult,
        array $includedItemTypeResolverMockValueMap,
        array $contentItemsToUpdate
    ): void {
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockContentServiceUpdateContentItems($contentItemsToUpdate);
        $this->mockLocationSearch($locationSearchResult);

        $this->locationEventSubscriber->onUpdateLocation(
            new UpdateLocationEvent(
                $this->createMock(Location::class),
                $location,
                $this->createMock(LocationUpdateStruct::class)
            )
        );
    }

    /**
     * @dataProvider provideDataLocationWithChildren
     *
     * @phpstan-param TIsContentIncludedValueMap $includedItemTypeResolverMockValueMap
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $contentsToDelete
     */
    public function testOnHideLocation(
        Location $location,
        SearchResult $locationSearchResult,
        array $includedItemTypeResolverMockValueMap,
        array $contentsToDelete
    ): void {
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockContentServiceDeleteContentItems($contentsToDelete);
        $this->mockLocationSearch($locationSearchResult);

        $this->locationEventSubscriber->onHideLocation(
            new HideLocationEvent(
                $this->createMock(Location::class),
                $location
            )
        );
    }

    /**
     * @dataProvider provideDataForLocationSwap
     *
     * @phpstan-param TIsContentIncludedValueMap $includedItemTypeResolverMockValueMap
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $expectedContentItemsToUpdate
     */
    public function testCallOnSwapLocationMethod(
        Location $location,
        Location $swapLocation,
        SearchResult $locationChildrenSearchResult,
        SearchResult $swapLocationChildrenSearchResult,
        array $includedItemTypeResolverMockValueMap,
        array $expectedContentItemsToUpdate
    ): void {
        $this->searchService
            ->method('findLocations')
            ->willReturnOnConsecutiveCalls(
                $locationChildrenSearchResult,
                $swapLocationChildrenSearchResult,
            );

        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockContentServiceUpdateContentItems($expectedContentItemsToUpdate);

        $this->locationEventSubscriber->onSwapLocation(
            new SwapLocationEvent(
                $location,
                $swapLocation
            )
        );
    }

    public function testOnCreateLocation(): void
    {
        $content = $this->createMock(Content::class);
        $location = $this->createLocation($content);

        $this->mockIncludedItemTypeResolverIsContentIncluded(
            [
                [$content, true],
            ]
        );
        $this->mockContentServiceUpdateContent($content);

        $this->locationEventSubscriber->onCreateLocation(
            new CreateLocationEvent(
                $location,
                $this->createMock(ContentInfo::class),
                $this->createMock(LocationCreateStruct::class)
            )
        );
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Location,
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult,
     *     TIsContentIncludedValueMap,
     *     array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>
     * }>
     */
    public function provideDataLocationWithChildren(): iterable
    {
        $content = $this->createMock(Content::class);
        $location = $this->createLocation($content);

        yield 'Content without children' => [
            $location,
            new SearchResult(['searchHits' => []]),
            [
                [$content, true],
            ],
            [$content],
        ];

        $childContentItems = $this->getChildContentItems();
        $locationSearchResult = $this->getLocationSearchResult(...$childContentItems);

        yield 'Content with children' => [
            $location,
            $locationSearchResult,
            $this->getBaseIsContentIncludedValueMap($content, $childContentItems),
            [$content, $childContentItems[0], $childContentItems[1]],
        ];
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Location,
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Location,
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult,
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult,
     *     TIsContentIncludedValueMap,
     *     array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>
     * }>
     */
    public function provideDataForLocationSwap(): iterable
    {
        $content = $this->createMock(Content::class);
        $location = new CoreLocation([
            'id' => 3,
            'pathString' => '/1/2/3/',
            'content' => $content,
        ]);

        $swappedContent = $this->createMock(Content::class);
        $swappedLocation = new CoreLocation([
            'id' => 6,
            'pathString' => '/1/2/6/',
            'content' => $swappedContent,
        ]);

        yield 'Content without children' => [
            $location,
            $swappedLocation,
            new SearchResult(['searchHits' => []]),
            new SearchResult(['searchHits' => []]),
            [
                [$content, true],
                [$swappedContent, true],
            ],
            [
                $content,
                $swappedContent,
            ],
        ];

        $childContent1 = $this->createMock(Content::class);
        $childContent2 = $this->createMock(Content::class);
        $swappedChildContent = $this->createMock(Content::class);

        yield 'Content with children' => [
            $location,
            $swappedLocation,
            new SearchResult([
                'searchHits' => [
                    new SearchHit([
                        'valueObject' => new CoreLocation([
                            'id' => 4,
                            'pathString' => '/1/2/3/4/',
                            'content' => $childContent1,
                        ]),
                    ]),
                    new SearchHit([
                        'valueObject' => new CoreLocation([
                            'id' => 5,
                            'pathString' => '/1/2/3/5/',
                            'content' => $childContent2,
                        ]),
                    ]),
                ],
                'totalCount' => 2,
            ]),
            new SearchResult([
                'searchHits' => [
                    new SearchHit([
                        'valueObject' => new CoreLocation([
                            'id' => 7,
                            'pathString' => '/1/2/6/7/',
                            'content' => $swappedChildContent,
                        ]),
                    ]),
                ],
                'totalCount' => 1,
            ]),
            [
                [$content, true],
                [$childContent1, true],
                [$childContent2, true],
                [$swappedContent, true],
                [$swappedChildContent, false],
            ],
            [
                $content,
                $childContent1,
                $childContent2,
                $swappedContent,
            ],
        ];
    }
}

class_alias(LocationEventSubscriberTest::class, 'EzSystems\EzRecommendationClient\Tests\Event\Subscriber\LocationEventSubscriberTest');
