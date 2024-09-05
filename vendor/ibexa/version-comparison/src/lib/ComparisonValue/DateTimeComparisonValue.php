<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\ComparisonValue;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class DateTimeComparisonValue extends ValueObject
{
    /** @var \DateTime|null */
    public $value;
}

class_alias(DateTimeComparisonValue::class, 'EzSystems\EzPlatformVersionComparison\ComparisonValue\DateTimeComparisonValue');
