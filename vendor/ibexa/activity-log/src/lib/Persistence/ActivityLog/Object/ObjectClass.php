<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence\ActivityLog\Object;

final class ObjectClass
{
    /** @readonly */
    public int $id;

    /**
     * @readonly
     *
     * @phpstan-var class-string
     */
    public string $className;

    /**
     * @phpstan-param class-string $className
     */
    public function __construct(
        int $id,
        string $className
    ) {
        $this->id = $id;
        $this->className = $className;
    }
}
