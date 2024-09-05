<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Segmentation\Event;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Segmentation\Value\Segment;

final class AssignUserToSegmentEvent extends AfterEvent
{
    private User $user;

    private Segment $segment;

    public function __construct(User $user, Segment $segment)
    {
        $this->user = $user;
        $this->segment = $segment;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getSegment(): Segment
    {
        return $this->segment;
    }
}
