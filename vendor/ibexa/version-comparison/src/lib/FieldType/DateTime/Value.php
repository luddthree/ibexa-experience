<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\DateTime;

use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;

class Value extends FieldTypeComparisonValue
{
    /** @var \Ibexa\VersionComparison\ComparisonValue\DateTimeComparisonValue */
    public $dateTime;

    /** @var \Ibexa\VersionComparison\ComparisonValue\IntegerComparisonValue|null */
    public $time;
}

class_alias(Value::class, 'EzSystems\EzPlatformVersionComparison\FieldType\DateTime\Value');
