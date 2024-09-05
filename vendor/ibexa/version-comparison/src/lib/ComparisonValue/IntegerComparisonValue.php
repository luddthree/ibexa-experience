<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\ComparisonValue;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class IntegerComparisonValue extends ValueObject
{
    /** @var int|null */
    public $value;
}

class_alias(IntegerComparisonValue::class, 'EzSystems\EzPlatformVersionComparison\ComparisonValue\IntegerComparisonValue');
