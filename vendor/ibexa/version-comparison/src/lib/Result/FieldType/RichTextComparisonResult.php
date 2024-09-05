<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\HtmlComparisonResult;

final class RichTextComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $htmlComparisonResult;

    public function __construct(HtmlComparisonResult $htmlComparisonResult)
    {
        $this->htmlComparisonResult = $htmlComparisonResult;
    }

    public function getRichTextDiff(): HtmlComparisonResult
    {
        return $this->htmlComparisonResult;
    }

    public function isChanged(): bool
    {
        return $this->htmlComparisonResult->isChanged();
    }
}

class_alias(RichTextComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\RichTextComparisonResult');
