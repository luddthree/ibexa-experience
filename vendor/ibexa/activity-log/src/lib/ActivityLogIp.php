<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogIpInterface;

final class ActivityLogIp implements ActivityLogIpInterface
{
    private int $id;

    private string $ip;

    public function __construct(int $id, string $ip)
    {
        $this->id = $id;
        $this->ip = $ip;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIp(): string
    {
        return $this->ip;
    }
}
