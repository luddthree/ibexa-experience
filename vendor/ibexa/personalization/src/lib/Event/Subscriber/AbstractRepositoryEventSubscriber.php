<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\ContentService as CoreContentService;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter\LocationSearchAdapter;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\RelationList;
use Ibexa\Core\Query\QueryFactoryInterface;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Service\Content\ContentServiceInterface;

abstract class AbstractRepositoryEventSubscriber
{
    protected CoreContentService $coreContentService;

    protected ContentServiceInterface $contentService;

    protected IncludedItemTypeResolverInterface $includedItemTypeResolver;

    protected LocationService $locationService;

    protected Repository $repository;

    protected QueryFactoryInterface $queryFactory;

    protected SearchService $searchService;

    public function __construct(
        CoreContentService $coreContentService,
        ContentServiceInterface $contentService,
        IncludedItemTypeResolverInterface $includedItemTypeResolver,
        LocationService $locationService,
        Repository $repository,
        QueryFactoryInterface $queryFactory,
        SearchService $searchService
    ) {
        $this->coreContentService = $coreContentService;
        $this->contentService = $contentService;
        $this->includedItemTypeResolver = $includedItemTypeResolver;
        $this->locationService = $locationService;
        $this->repository = $repository;
        $this->queryFactory = $queryFactory;
        $this->searchService = $searchService;
    }

    /**
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>
     */
    final protected function getLocationChildrenContentItems(
        Location $location,
        bool $useHidden = false
    ): array {
        $contentItems = [];

        $query = new LocationQuery([
            'query' => new Criterion\LogicalAnd([
                new Criterion\Subtree($location->getPathString()),
                new Criterion\LogicalNot(new Criterion\LocationId($location->id)),
            ]),
        ]);

        $subtreeBatchIterator = new BatchIterator(new LocationSearchAdapter(
            $this->searchService,
            $query,
            [],
            false
        ));

        foreach ($subtreeBatchIterator as $searchHit) {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $childLocation */
            $childLocation = $searchHit->valueObject;

            if (!$useHidden && $childLocation->hidden) {
                continue;
            }

            if ($this->includedItemTypeResolver->isContentIncluded($childLocation->getContent())) {
                $contentItems[] = $childLocation->getContent();
            }
        }

        return $contentItems;
    }

    final protected function updateLocationWithChildren(
        Location $location,
        bool $useHidden = false
    ): void {
        $content = $location->getContent();
        if (!$this->includedItemTypeResolver->isContentIncluded($content)) {
            return;
        }

        $this->contentService->updateContentItems(
            array_merge(
                [$content],
                $this->getLocationChildrenContentItems($location, $useHidden),
            )
        );
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Location> $locations
     */
    final protected function updateLocationsWithChildren(
        array $locations,
        bool $useHidden = false
    ): void {
        $this->contentService->updateContentItems(
            $this->getContentItemsLocationsWithChildren($locations, $useHidden)
        );
    }

    final protected function updateContentWithChildrenFromAllLocationsAndRelations(
        ContentInfo $contentInfo,
        bool $useHidden = false
    ): void {
        $this->contentService->updateContentItems(
            array_merge(
                $this->getContentItemsLocationsWithChildren(
                    $this->getLocations($contentInfo),
                    $useHidden
                ),
                $this->getContentRelations($contentInfo)
            )
        );
    }

    final protected function deleteLocationWithChildren(
        Location $location,
        bool $useHidden = false
    ): void {
        $content = $location->getContent();
        if (!$this->includedItemTypeResolver->isContentIncluded($content)) {
            return;
        }

        $this->contentService->deleteContentItems(
            array_merge(
                [$content],
                $this->getLocationChildrenContentItems($location, $useHidden),
            )
        );
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Location> $locations
     */
    final protected function deleteLocationsWithChildren(
        array $locations,
        bool $useHidden = false
    ): void {
        $this->contentService->deleteContentItems(
            $this->getContentItemsLocationsWithChildren($locations, $useHidden)
        );
    }

    final protected function deleteContentWithChildrenFromAllLocations(
        ContentInfo $contentInfo,
        bool $useHidden = false
    ): void {
        $this->contentService->deleteContentItems(
            $this->getContentItemsLocationsWithChildren(
                $this->getLocations($contentInfo),
                $useHidden
            )
        );
    }

    /**
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Location> $locations
     *
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>
     */
    private function getContentItemsLocationsWithChildren(
        iterable $locations,
        bool $useHidden = false
    ): array {
        $contentItems = [];

        foreach ($locations as $location) {
            $content = $location->getContent();
            if (!$this->includedItemTypeResolver->isContentIncluded($content)) {
                continue;
            }

            $contentItems[] = $content;
            array_push($contentItems, ...$this->getLocationChildrenContentItems($location, $useHidden));
        }

        return $contentItems;
    }

    /**
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>
     */
    protected function getContentRelations(ContentInfo $contentInfo): array
    {
        $contentItems = [];
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\RelationList\Item\RelationListItem $relationListItem */
        foreach ($this->getRelationList($contentInfo) as $relationListItem) {
            $relation = $relationListItem->getRelation();
            if (null === $relation) {
                continue;
            }

            $content = $this->repository->sudo(
                fn (): Content => $this->coreContentService->loadContent(
                    $relation->getSourceContentInfo()->getId()
                )
            );

            if ($this->includedItemTypeResolver->isContentIncluded($content)) {
                $contentItems[] = $content;
            }
        }

        return $contentItems;
    }

    /**
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Location>
     */
    private function getLocations(ContentInfo $contentInfo): iterable
    {
        /** Sudo must be used to load all location children, since Client using this EventSubscriber operates as a User without privileges. */
        return $this->repository->sudo(
            fn (): iterable => $this->locationService->loadLocations($contentInfo)
        );
    }

    private function getRelationList(ContentInfo $contentInfo): RelationList
    {
        /** Sudo must be used to load all content relations, since Client using this EventSubscriber operates as a User without privileges. */
        return $this->repository->sudo(
            fn (): RelationList => $this->coreContentService->loadReverseRelationList($contentInfo)
        );
    }
}

class_alias(AbstractRepositoryEventSubscriber::class, 'EzSystems\EzRecommendationClient\Event\Subscriber\AbstractRepositoryEventSubscriber');
