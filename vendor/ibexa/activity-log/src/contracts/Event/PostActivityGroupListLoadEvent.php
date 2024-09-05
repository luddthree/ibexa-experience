<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Event;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityGroupListInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class PostActivityGroupListLoadEvent extends Event
{
    private ActivityGroupListInterface $list;

    public function __construct(ActivityGroupListInterface $list)
    {
        $this->list = $list;
    }

    public function getList(): ActivityGroupListInterface
    {
        return $this->list;
    }
}
