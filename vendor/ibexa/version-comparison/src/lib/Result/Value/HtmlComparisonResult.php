<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;

final class HtmlComparisonResult implements ComparisonResult
{
    /** @var string */
    private $htmlDiff;

    /** @var bool */
    private $isChanged;

    public function __construct(string $htmlDiff, bool $isChanged)
    {
        $this->htmlDiff = $htmlDiff;
        $this->isChanged = $isChanged;
    }

    public function getHtmlDiff(): string
    {
        return $this->htmlDiff;
    }

    public function isChanged(): bool
    {
        return $this->isChanged;
    }
}

class_alias(HtmlComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\HtmlComparisonResult');
