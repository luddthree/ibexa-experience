<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

final class UrlComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $linkDiff;

    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $textDiff;

    public function __construct(StringComparisonResult $linkDiff, StringComparisonResult $textDiff)
    {
        $this->linkDiff = $linkDiff;
        $this->textDiff = $textDiff;
    }

    public function getLinkComparisonResult(): StringComparisonResult
    {
        return $this->linkDiff;
    }

    public function getTextComparisonResult(): StringComparisonResult
    {
        return $this->textDiff;
    }

    public function isChanged(): bool
    {
        return $this->linkDiff->isChanged() || $this->textDiff->isChanged();
    }
}

class_alias(UrlComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\UrlComparisonResult');
