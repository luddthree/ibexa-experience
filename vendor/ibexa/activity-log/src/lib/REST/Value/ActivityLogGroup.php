<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Value;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface;

final class ActivityLogGroup
{
    /**
     * @readonly
     */
    public ActivityLogGroupInterface $group;

    public function __construct(ActivityLogGroupInterface $group)
    {
        $this->group = $group;
    }

    /**
     * @return iterable<\Ibexa\ActivityLog\REST\Value\ActivityLog>
     */
    public function getLogEntries(): iterable
    {
        foreach ($this->group->getActivityLogs() as $log) {
            yield new ActivityLog($log);
        }
    }
}
