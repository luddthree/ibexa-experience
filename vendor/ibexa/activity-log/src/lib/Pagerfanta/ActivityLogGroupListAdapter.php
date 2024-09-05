<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Pagerfanta;

use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Pagerfanta\Adapter\AdapterInterface;

final class ActivityLogGroupListAdapter implements AdapterInterface
{
    private ActivityLogServiceInterface $activityLogService;

    private Query $query;

    public function __construct(ActivityLogServiceInterface $activityLogService, ?Query $query = null)
    {
        $this->activityLogService = $activityLogService;
        $this->query = $query ?? new Query();
    }

    public function getNbResults(): int
    {
        return $this->activityLogService->countGroups($this->query);
    }

    /**
     * @phpstan-return \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface[]
     */
    public function getSlice($offset, $length): array
    {
        $query = clone $this->query;
        $query->limit = $length;
        $query->offset = $offset;

        return $this->activityLogService->findGroups($query)->getActivityLogs();
    }
}
