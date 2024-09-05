<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface;

/**
 * @template T of object
 *
 * @implements \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface<T>
 */
final class ObjectClass implements ObjectClassInterface
{
    private int $id;

    private ?string $shortName;

    /** @var class-string<T> */
    private string $objectClass;

    /**
     * @phpstan-param class-string<T> $objectClass
     */
    public function __construct(
        int $id,
        ?string $shortName,
        string $objectClass
    ) {
        $this->id = $id;
        $this->shortName = $shortName;
        $this->objectClass = $objectClass;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function getObjectClass(): string
    {
        return $this->objectClass;
    }
}
