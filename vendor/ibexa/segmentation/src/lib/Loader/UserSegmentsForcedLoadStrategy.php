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
final class UserSegmentsForcedLoadStrategy implements UserSegmentsLoadStrategyInterface
{
    /** @var \Ibexa\Segmentation\Persistence\Handler\HandlerInterface */
    private $handler;

    /** @var array<int, array<\Ibexa\Segmentation\Value\Persistence\Segment>> */
    private $segments;

    public function __construct(
        HandlerInterface $handler,
        array $segments = []
    ) {
        $this->handler = $handler;
        $this->segments = $segments;
    }

    public function loadUserAssignedSegments(User $user): array
    {
        $userId = $user->getUserId();

        return $this->segments[$userId] ?? $this->handler->loadSegmentsAssignedToUser($userId);
    }

    /**
     * @param \Ibexa\Segmentation\Value\Persistence\Segment[] $segments
     */
    public function setSegments(int $userId, array $segments): void
    {
        $this->segments[$userId] = $segments;
    }
}

class_alias(UserSegmentsForcedLoadStrategy::class, 'Ibexa\Platform\Segmentation\Loader\UserSegmentsForcedLoadStrategy');
