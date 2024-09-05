<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Loader;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Segmentation\Persistence\Handler\HandlerInterface;

/**
 * @internal
 */
final class UserSegmentsLoadStrategy implements UserSegmentsLoadStrategyInterface
{
    /** @var \Ibexa\Segmentation\Persistence\Handler\HandlerInterface */
    private $handler;

    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function loadUserAssignedSegments(User $user): array
    {
        return $this->handler->loadSegmentsAssignedToUser($user->id);
    }
}

class_alias(UserSegmentsLoadStrategy::class, 'Ibexa\Platform\Segmentation\Loader\UserSegmentsLoadStrategy');
