<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog;

/**
 * @template T of object
 */
interface ObjectClassInterface
{
    public function getId(): int;

    /** @phpstan-return class-string<T> */
    public function getObjectClass(): string;

    public function getShortName(): ?string;
}
