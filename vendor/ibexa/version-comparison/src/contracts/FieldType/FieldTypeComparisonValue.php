<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\VersionComparison\FieldType;

use Ibexa\Contracts\Core\Persistence\ValueObject;

abstract class FieldTypeComparisonValue extends ValueObject
{
    public function getType(): string
    {
        return static::class;
    }
}

class_alias(FieldTypeComparisonValue::class, 'EzSystems\EzPlatformVersionComparison\FieldType\FieldTypeComparisonValue');
