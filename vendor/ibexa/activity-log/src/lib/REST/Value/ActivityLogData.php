<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Value;

use Ibexa\Contracts\Core\Collection\MapInterface;

final class ActivityLogData
{
    /**
     * @readonly
     *
     * @phpstan-var \Ibexa\Contracts\Core\Collection\MapInterface<string, mixed>
     */
    public MapInterface $data;

    /**
     * @phpstan-param \Ibexa\Contracts\Core\Collection\MapInterface<string, mixed> $data
     */
    public function __construct(MapInterface $data)
    {
        $this->data = $data;
    }
}
