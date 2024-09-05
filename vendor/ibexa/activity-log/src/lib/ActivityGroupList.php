<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog;

use ArrayIterator;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityGroupListInterface;
use Traversable;

final class ActivityGroupList implements ActivityGroupListInterface
{
    /**
     * @var \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface[]
     */
    private array $activityLogs;

    /**
     * @param \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface[] $activityLogs
     */
    public function __construct(array $activityLogs)
    {
        $this->activityLogs = $activityLogs;
    }

    public function getActivityLogs(): array
    {
        return $this->activityLogs;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->activityLogs);
    }

    public function count(): int
    {
        return count($this->activityLogs);
    }
}
