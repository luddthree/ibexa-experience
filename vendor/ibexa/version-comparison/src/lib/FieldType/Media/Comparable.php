<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\Media;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\VersionComparison\FieldType\Comparable as ComparableInterface;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\BinaryFileComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\BooleanComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\IntegerComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\StringComparisonValue;

final class Comparable implements ComparableInterface
{
    /**
     * @param \Ibexa\Core\FieldType\Media\Value $value
     */
    public function getDataToCompare(SPIValue $value): FieldTypeComparisonValue
    {
        return new Value([
            'fileName' => new StringComparisonValue([
                'value' => $value->fileName,
                'doNotSplit' => true,
            ]),
            'file' => new BinaryFileComparisonValue([
                'id' => $value->id,
                'path' => $value->uri,
                'size' => $value->fileSize,
            ]),
            'fileSize' => new IntegerComparisonValue([
                'value' => $value->fileSize,
            ]),
            'autoplay' => new BooleanComparisonValue([
                'value' => $value->autoplay,
            ]),
            'hasController' => new BooleanComparisonValue([
                'value' => $value->hasController,
            ]),
            'loop' => new BooleanComparisonValue([
                'value' => $value->loop,
            ]),
            'mimeType' => new StringComparisonValue([
                'value' => $value->mimeType,
                'doNotSplit' => true,
            ]),
        ]);
    }
}

class_alias(Comparable::class, 'EzSystems\EzPlatformVersionComparison\FieldType\Media\Comparable');
