<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event\Subscriber;

use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Repository\ContentService as CoreContentService;
use Ibexa\Contracts\Core\Repository\Events\Trash\BeforeTrashEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\RecoverEvent;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionCriterionResolver;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd as CriterionLogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalNot as CriterionLogicalNot;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree as CriterionSubtree;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\Query\QueryFactoryInterface;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Event\PersonalizationEvent;
use Ibexa\Personalization\Service\Content\ContentServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class TrashEventSubscriber extends AbstractRepositoryEventSubscriber implements EventSubscriberInterface
{
    private PermissionResolver $permissionResolver;

    private PermissionCriterionResolver $permissionCriterionResolver;

    private SettingServiceInterface $settingService;

    public function __construct(
        CoreContentService $coreContentService,
        ContentServiceInterface $contentService,
        IncludedItemTypeResolverInterface $includedItemTypeResolver,
        LocationService $locationService,
        Repository $repository,
        PermissionResolver $permissionResolver,
        PermissionCriterionResolver $permissionCriterionResolver,
        SearchService $searchService,
        SettingServiceInterface $settingService,
        QueryFactoryInterface $queryFactory
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

        $this->permissionResolver = $permissionResolver;
        $this->permissionCriterionResolver = $permissionCriterionResolver;
        $this->settingService = $settingService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RecoverEvent::class => ['onRecover', PersonalizationEvent::DEFAULT_PRIORITY],
            BeforeTrashEvent::class => ['onBeforeTrash', PersonalizationEvent::DEFAULT_PRIORITY],
        ];
    }

    public function onRecover(RecoverEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $location = $event->getLocation();
        $content = $location->getContent();
        if (!$this->includedItemTypeResolver->isContentIncluded($content)) {
            return;
        }

        $contentItems = array_merge(
            [$content],
            $this->getContentRelations($location->getContentInfo()),
        );

        $this->contentService->updateContentItems($contentItems);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function onBeforeTrash(BeforeTrashEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $location = $event->getLocation();
        $contentInfo = $location->getContentInfo();
        if (!$this->canDelete($contentInfo, $location)) {
            throw new UnauthorizedException('content', 'remove');
        }

        $this->deleteLocationWithChildren($location);

        $relations = $this->getContentRelations($location->getContentInfo());
        if (empty($relations)) {
            return;
        }

        $this->contentService->updateContentItems($relations);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function canDelete(ContentInfo $contentInfo, Location $location): bool
    {
        if (!$this->hasContentRemovePermission($contentInfo, $location)) {
            return false;
        }

        return $this->hasSubtreeRemovePermission($location);
    }

    private function hasContentRemovePermission(ContentInfo $contentInfo, Location $location): bool
    {
        $target = (new Target\Version())->deleteTranslations($location->getContent()->getVersionInfo()->languageCodes);

        return $this->permissionResolver->canUser('content', 'remove', $contentInfo, [$location, $target]);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException
     */
    private function hasSubtreeRemovePermission(Location $location): bool
    {
        $contentRemoveCriterion = $this->permissionCriterionResolver->getPermissionsCriterion('content', 'remove');
        if (is_bool($contentRemoveCriterion)) {
            return $contentRemoveCriterion;
        }

        $result = $this->searchService->findContent(
            $this->getQuery($location, $contentRemoveCriterion),
            [],
            false
        );

        return $result->totalCount === 0;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException
     */
    private function getQuery(Location $location, Criterion $criterion): Query
    {
        return new Query(
            [
                'limit' => 0,
                'filter' => new CriterionLogicalAnd(
                    [
                        new CriterionSubtree($location->getPathString()),
                        new CriterionLogicalNot($criterion),
                    ]
                ),
            ]
        );
    }
}

class_alias(TrashEventSubscriber::class, 'EzSystems\EzRecommendationClient\Event\Subscriber\TrashEventSubscriber');
