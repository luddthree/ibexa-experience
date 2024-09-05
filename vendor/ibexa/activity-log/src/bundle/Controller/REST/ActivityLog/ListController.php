<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Controller\REST\ActivityLog;

use Ibexa\ActivityLog\REST\Value\ActivityLogGroupList;
use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Rest\Server\Controller;

final class ListController extends Controller
{
    private ActivityLogServiceInterface $activityLogService;

    public function __construct(ActivityLogServiceInterface $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function list(Query $query): ActivityLogGroupList
    {
        $list = $this->activityLogService->findGroups($query);

        return new ActivityLogGroupList($list);
    }
}
