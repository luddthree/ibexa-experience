<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\ComparisonValue;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class Collection extends ValueObject
{
    /**
     * Array that is compared with another based on values.
     *
     * @var array|null
     */
    public $collection;

    /**
     * Callable used for array_udiff and array_uintersect function.
     * If not provided, collection are compared by values.
     *
     * @var callable|null
     */
    public $compareCallable;
}

class_alias(Collection::class, 'EzSystems\EzPlatformVersionComparison\ComparisonValue\Collection');
