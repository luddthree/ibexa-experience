<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Loader;

use Ibexa\Contracts\Core\Repository\Values\User\User;

/**
 * @internal
 */
interface UserSegmentsLoadStrategyInterface
{
    /**
     * @return \Ibexa\Segmentation\Value\Persistence\Segment[]
     */
    public function loadUserAssignedSegments(User $user): array;
}

class_alias(UserSegmentsLoadStrategyInterface::class, 'Ibexa\Platform\Segmentation\Loader\UserSegmentsLoadStrategyInterface');
