<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\Media;

use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;

class Value extends FieldTypeComparisonValue
{
    /** @var \Ibexa\VersionComparison\ComparisonValue\StringComparisonValue */
    public $fileName;

    /** @var \Ibexa\VersionComparison\ComparisonValue\IntegerComparisonValue */
    public $fileSize;

    /** @var \Ibexa\VersionComparison\ComparisonValue\BinaryFileComparisonValue */
    public $file;

    /** @var \Ibexa\VersionComparison\ComparisonValue\BooleanComparisonValue */
    public $autoplay;

    /** @var \Ibexa\VersionComparison\ComparisonValue\BooleanComparisonValue */
    public $hasController;

    /** @var \Ibexa\VersionComparison\ComparisonValue\BooleanComparisonValue */
    public $loop;

    /** @var \Ibexa\VersionComparison\ComparisonValue\StringComparisonValue */
    public $mimeType;
}

class_alias(Value::class, 'EzSystems\EzPlatformVersionComparison\FieldType\Media\Value');
