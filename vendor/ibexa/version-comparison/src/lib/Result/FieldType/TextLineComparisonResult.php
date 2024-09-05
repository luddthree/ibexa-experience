<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

final class TextLineComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $stringDiff;

    public function __construct(StringComparisonResult $stringDiff)
    {
        $this->stringDiff = $stringDiff;
    }

    public function getTextLineDiff(): StringComparisonResult
    {
        return $this->stringDiff;
    }

    public function isChanged(): bool
    {
        return $this->stringDiff->isChanged();
    }
}

class_alias(TextLineComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\TextLineComparisonResult');
