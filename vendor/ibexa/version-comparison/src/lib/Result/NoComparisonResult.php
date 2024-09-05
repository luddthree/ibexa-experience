<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;

/**
 * Used when comparison was not run for given FieldType.
 */
final class NoComparisonResult implements ComparisonResult
{
    public function isChanged(): bool
    {
        return false;
    }
}

class_alias(NoComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\NoComparisonResult');
