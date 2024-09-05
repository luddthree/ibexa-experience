<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\ImageAsset;

use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;

class Value extends FieldTypeComparisonValue
{
    /** @var \Ibexa\VersionComparison\ComparisonValue\StringComparisonValue */
    public $fileName;

    /** @var \Ibexa\VersionComparison\ComparisonValue\IntegerComparisonValue */
    public $destinationContentId;

    /** @var \Ibexa\VersionComparison\ComparisonValue\StringComparisonValue */
    public $alternativeText;

    /** @var \Ibexa\VersionComparison\ComparisonValue\BinaryFileComparisonValue */
    public $file;
}

class_alias(Value::class, 'EzSystems\EzPlatformVersionComparison\FieldType\ImageAsset\Value');
