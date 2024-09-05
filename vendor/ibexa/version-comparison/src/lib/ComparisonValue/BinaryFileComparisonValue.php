<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\ComparisonValue;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class BinaryFileComparisonValue extends ValueObject
{
    /** @var string|null */
    public $id;

    /** @var string|null */
    public $path;

    /** @var int|null */
    public $size;
}

class_alias(BinaryFileComparisonValue::class, 'EzSystems\EzPlatformVersionComparison\ComparisonValue\BinaryFileComparisonValue');
