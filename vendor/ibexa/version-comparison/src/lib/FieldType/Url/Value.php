<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\Url;

use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;

class Value extends FieldTypeComparisonValue
{
    /** @var \Ibexa\VersionComparison\ComparisonValue\StringComparisonValue */
    public $link;

    /** @var \Ibexa\VersionComparison\ComparisonValue\StringComparisonValue */
    public $text;
}

class_alias(Value::class, 'EzSystems\EzPlatformVersionComparison\FieldType\Url\Value');
