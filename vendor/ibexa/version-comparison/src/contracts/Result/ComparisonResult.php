<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\VersionComparison\Result;

interface ComparisonResult
{
    public function isChanged(): bool;
}

class_alias(ComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\ComparisonResult');
