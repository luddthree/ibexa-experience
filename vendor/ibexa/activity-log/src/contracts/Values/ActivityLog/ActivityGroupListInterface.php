<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog;

use Countable;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface>
 */
interface ActivityGroupListInterface extends IteratorAggregate, Countable
{
    /**
     * Partial list of activity logs.
     *
     * @return \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface[]
     */
    public function getActivityLogs(): array;
}
