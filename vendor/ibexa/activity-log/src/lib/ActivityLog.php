<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActionInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface;
use Ibexa\Contracts\Core\Collection\MapInterface;

/**
 * @template T of object
 *
 * @phpstan-implements \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogInterface<T>
 */
final class ActivityLog implements ActivityLogInterface
{
    /** @phpstan-var T|null */
    private ?object $relatedObject = null;

    /** @var ObjectClassInterface<T> */
    private ObjectClassInterface $objectClass;

    private string $objectId;

    private ActionInterface $action;

    private ?string $objectName;

    /** @var \Ibexa\Contracts\Core\Collection\MapInterface<string, mixed> */
    private MapInterface $data;

    /**
     * @phpstan-param \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface<T> $objectClass
     * @phpstan-param \Ibexa\Contracts\Core\Collection\MapInterface<string, mixed> $data
     */
    public function __construct(
        ObjectClassInterface $objectClass,
        string $objectId,
        ActionInterface $action,
        ?string $objectName,
        MapInterface $data
    ) {
        $this->objectClass = $objectClass;
        $this->objectId = $objectId;
        $this->action = $action;
        $this->objectName = $objectName;
        $this->data = $data;
    }

    /**
     * @phpstan-param T $object
     */
    public function setRelatedObject(object $object): void
    {
        $this->relatedObject = $object;
    }

    public function getRelatedObject(): ?object
    {
        return $this->relatedObject;
    }

    public function getAction(): string
    {
        return $this->action->getName();
    }

    public function getObjectClass(): string
    {
        return $this->objectClass->getObjectClass();
    }

    public function getShortObjectClass(): ?string
    {
        return $this->objectClass->getShortName();
    }

    public function getObjectId(): string
    {
        return $this->objectId;
    }

    public function getObjectName(): ?string
    {
        return $this->objectName;
    }

    public function getData(): MapInterface
    {
        return $this->data;
    }
}
