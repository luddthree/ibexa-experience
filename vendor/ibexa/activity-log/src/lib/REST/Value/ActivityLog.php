<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Value;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogInterface;
use Ibexa\Rest\Value as RestValue;

final class ActivityLog extends RestValue
{
    /**
     * @readonly
     *
     * @var \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogInterface<object>
     */
    public ActivityLogInterface $activityLog;

    /**
     * @param \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogInterface<object> $activityLog
     */
    public function __construct(ActivityLogInterface $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function getData(): ActivityLogData
    {
        return new ActivityLogData($this->activityLog->getData());
    }
}
