<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion;

final class ObjectCriterion implements CriterionInterface
{
    /** @phpstan-var class-string */
    public string $objectClass;

    /** @var array<string>|null */
    public ?array $ids;

    /**
     * @phpstan-param class-string $objectClass
     *
     * @param array<string>|null $ids
     */
    public function __construct(string $objectClass, ?array $ids = null)
    {
        $this->objectClass = $objectClass;
        $this->ids = $ids;
    }
}
