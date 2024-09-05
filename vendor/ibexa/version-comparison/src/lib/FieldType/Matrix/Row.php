<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\Matrix;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class Row extends ValueObject
{
    /** @var \Ibexa\VersionComparison\ComparisonValue\StringComparisonValue[] */
    public $cells;
}

class_alias(Row::class, 'EzSystems\EzPlatformVersionComparison\FieldType\Matrix\Row');
