<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Rest\Value;

final class UserSegmentsList extends Value
{
    public User $user;

    /** @var \Ibexa\Bundle\Segmentation\REST\Value\UserSegment[] */
    public array $segments;

    /**
     * @param \Ibexa\Bundle\Segmentation\REST\Value\UserSegment[] $segments
     */
    public function __construct(User $user, array $segments)
    {
        $this->user = $user;
        $this->segments = $segments;
    }
}
