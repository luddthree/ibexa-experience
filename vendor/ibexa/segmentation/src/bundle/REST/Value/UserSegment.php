<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Rest\Value;
use Ibexa\Segmentation\Value\Segment;

final class UserSegment extends Value
{
    public User $user;

    public Segment $segment;

    public function __construct(User $user, Segment $segment)
    {
        $this->user = $user;
        $this->segment = $segment;
    }
}
