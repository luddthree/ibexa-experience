<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Value;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityGroupListInterface;
use Ibexa\Rest\Value as RestValue;
use IteratorAggregate;
use Traversable;

/**
 * @internal
 *
 * @implements \IteratorAggregate<\Ibexa\ActivityLog\REST\Value\ActivityLogGroup>
 */
final class ActivityLogGroupList extends RestValue implements IteratorAggregate
{
    public const MEDIA_TYPE = 'ActivityLogGroupList';

    /** @var \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityGroupListInterface */
    private ActivityGroupListInterface $activityList;

    /**
     * @param \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityGroupListInterface $activityList
     */
    public function __construct(ActivityGroupListInterface $activityList)
    {
        $this->activityList = $activityList;
    }

    public function getIterator(): Traversable
    {
        foreach ($this->activityList as $activityLog) {
            yield new ActivityLogGroup($activityLog);
        }
    }
}
