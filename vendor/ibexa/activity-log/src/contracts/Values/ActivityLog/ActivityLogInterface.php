<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog;

use Ibexa\Contracts\Core\Collection\MapInterface;

/**
 * @phpstan-template T of object
 */
interface ActivityLogInterface
{
    /**
     * @phpstan-param T $object
     */
    public function setRelatedObject(object $object): void;

    /**
     * This method can return null if the object is not accessible anymore, or current user does not have permissions to
     * access it.
     *
     * @phpstan-return T|null
     */
    public function getRelatedObject(): ?object;

    public function getAction(): string;

    /**
     * @phpstan-return class-string<T>
     */
    public function getObjectClass(): string;

    public function getShortObjectClass(): ?string;

    public function getObjectId(): string;

    public function getObjectName(): ?string;

    /**
     * @return \Ibexa\Contracts\Core\Collection\MapInterface<string, mixed>
     */
    public function getData(): MapInterface;
}
