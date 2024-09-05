<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\ContentService as CoreContentService;
use Ibexa\Contracts\Core\Repository\Events\Location\CopySubtreeEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\CreateLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\HideLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\MoveSubtreeEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\SwapLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\UnhideLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\UpdateLocationEvent;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Core\Query\QueryFactoryInterface;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Event\PersonalizationEvent;
use Ibexa\Personalization\Service\Content\ContentServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class LocationEventSubscriber extends AbstractRepositoryEventSubscriber implements EventSubscriberInterface
{
    private SettingServiceInterface $settingService;

    public function __construct(
        CoreContentService $coreContentService,
        ContentServiceInterface $contentService,
        IncludedItemTypeResolverInterface $includedItemTypeResolver,
        LocationService $locationService,
        Repository $repository,
        SettingServiceInterface $settingService,
        QueryFactoryInterface $queryFactory,
        SearchService $searchService
    ) {
        parent::__construct(
            $coreContentService,
            $contentService,
            $includedItemTypeResolver,
            $locationService,
            $repository,
            $queryFactory,
            $searchService
        );

        $this->settingService = $settingService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CopySubtreeEvent::class => ['onCopySubtree', PersonalizationEvent::DEFAULT_PRIORITY],
            CreateLocationEvent::class => ['onCreateLocation', PersonalizationEvent::DEFAULT_PRIORITY],
            HideLocationEvent::class => ['onHideLocation', PersonalizationEvent::DEFAULT_PRIORITY],
            MoveSubtreeEvent::class => ['onMoveSubtree', PersonalizationEvent::DEFAULT_PRIORITY],
            SwapLocationEvent::class => ['onSwapLocation', PersonalizationEvent::DEFAULT_PRIORITY],
            UnhideLocationEvent::class => ['onUnhideLocation', PersonalizationEvent::DEFAULT_PRIORITY],
            UpdateLocationEvent::class => ['onUpdateLocation', PersonalizationEvent::DEFAULT_PRIORITY],
        ];
    }

    public function onCopySubtree(CopySubtreeEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $this->updateLocationWithChildren($event->getLocation());
    }

    public function onCreateLocation(CreateLocationEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $content = $event->getLocation()->getContent();
        if (!$this->includedItemTypeResolver->isContentIncluded($content)) {
            return;
        }

        $this->contentService->updateContent($content);
    }

    public function onHideLocation(HideLocationEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $this->deleteLocationWithChildren($event->getLocation());
    }

    public function onMoveSubtree(MoveSubtreeEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $this->updateLocationWithChildren($event->getLocation());
    }

    public function onSwapLocation(SwapLocationEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $this->updateLocationsWithChildren(
            [
                $event->getLocation1(),
                $event->getLocation2(),
            ]
        );
    }

    public function onUnhideLocation(UnhideLocationEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $this->updateLocationWithChildren($event->getLocation(), true);
    }

    public function onUpdateLocation(UpdateLocationEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $this->updateLocationWithChildren($event->getLocation());
    }
}

class_alias(LocationEventSubscriber::class, 'EzSystems\EzRecommendationClient\Event\Subscriber\LocationEventSubscriber');
