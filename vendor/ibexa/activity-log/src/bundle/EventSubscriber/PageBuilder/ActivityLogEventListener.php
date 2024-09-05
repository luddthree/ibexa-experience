<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\EventSubscriber\PageBuilder;

use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LogicalOr;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ObjectCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\UserCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\LoggedAtSortClause;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\FieldTypePage\Event\PageEvents;
use Ibexa\FieldTypePage\Event\PageFromPersistenceEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @phpstan-type TParameters array{
 *     limit?: int,
 *     user?: \Ibexa\Contracts\Core\Repository\Values\User\User|\Ibexa\Contracts\Core\Repository\Values\User\User[],
 *     object_class?: \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface<object>|\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface<object>[]
 * }
 */
final class ActivityLogEventListener implements EventSubscriberInterface
{
    public const IBEXA_ACTIVITY_LOG_BLOCK = 'ibexa_activity_log_list';

    public const IBEXA_ACTIVITY_LOG_ATTRIBUTE_LIMIT = 'limit';
    public const IBEXA_ACTIVITY_LOG_ATTRIBUTE_USER = 'user';
    public const IBEXA_ACTIVITY_LOG_ATTRIBUTE_OBJECT_CLASS = 'object_class';

    private ActivityLogServiceInterface $activityLogService;

    public function __construct(
        ActivityLogServiceInterface $activityLogService
    ) {
        $this->activityLogService = $activityLogService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName(self::IBEXA_ACTIVITY_LOG_BLOCK) => ['preRenderEvent', -10],
            PageEvents::PERSISTENCE_FROM => ['onFromPersistence', -10],
        ];
    }

    public function preRenderEvent(PreRenderEvent $event): void
    {
        $renderRequest = $event->getRenderRequest();

        if (!$renderRequest instanceof TwigRenderRequest) {
            return;
        }

        $query = new Query();
        $query->sortClauses = [new LoggedAtSortClause(SortClauseInterface::DESC)];

        /** @phpstan-var TParameters $parameters */
        $parameters = $renderRequest->getParameters();

        $this->handleUserFilter($parameters, $query);
        $this->handleObjectClassFilter($parameters, $query);

        if (isset($parameters[self::IBEXA_ACTIVITY_LOG_ATTRIBUTE_LIMIT])) {
            $query->limit = $parameters[self::IBEXA_ACTIVITY_LOG_ATTRIBUTE_LIMIT];
        }

        $list = $this->activityLogService->findGroups($query);

        $renderRequest->addParameter('activity_logs_groups', $list);
    }

    public function onFromPersistence(PageFromPersistenceEvent $event): void
    {
        $pageValue = $event->getValue();

        $page = $pageValue->getPage();
        if (null === $page) {
            return;
        }

        foreach ($page->getZones() as $zone) {
            foreach ($zone->getBlocks() as $blockValue) {
                if (self::IBEXA_ACTIVITY_LOG_BLOCK !== $blockValue->getType()) {
                    continue;
                }

                $limitAttribute = $blockValue->getAttribute(self::IBEXA_ACTIVITY_LOG_ATTRIBUTE_LIMIT);
                if (null === $limitAttribute) {
                    continue;
                }

                $limitAttribute->setValue((int) $limitAttribute->getValue());
            }
        }
    }

    /**
     * @phpstan-param TParameters $parameters
     */
    private function handleUserFilter(array $parameters, Query $query): void
    {
        if (!isset($parameters[self::IBEXA_ACTIVITY_LOG_ATTRIBUTE_USER])) {
            return;
        }

        $users = $parameters[self::IBEXA_ACTIVITY_LOG_ATTRIBUTE_USER];
        if (!is_array($users)) {
            $users = [$users];
        }

        if (count($users) === 0) {
            return;
        }

        $ids = array_map(static fn (User $user): int => $user->getUserId(), $users);
        $query->criteria[] = new UserCriterion($ids);
    }

    /**
     * @phpstan-param TParameters $parameters
     */
    private function handleObjectClassFilter(array $parameters, Query $query): void
    {
        if (!isset($parameters[self::IBEXA_ACTIVITY_LOG_ATTRIBUTE_OBJECT_CLASS])) {
            return;
        }

        $objectClasses = $parameters[self::IBEXA_ACTIVITY_LOG_ATTRIBUTE_OBJECT_CLASS];

        if (!is_array($objectClasses)) {
            $objectClasses = [$objectClasses];
        }

        if (count($objectClasses) === 0) {
            return;
        }

        $query->criteria[] = new LogicalOr(array_map(
            static fn (ObjectClassInterface $objectClass) => new ObjectCriterion($objectClass->getObjectClass()),
            $objectClasses,
        ));
    }
}
