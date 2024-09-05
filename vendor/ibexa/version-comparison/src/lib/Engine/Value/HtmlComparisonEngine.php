<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\Value;

use Ibexa\VersionComparison\ComparisonValue\HtmlComparisonValue;
use Ibexa\VersionComparison\Engine\Value\Html\HtmlDiffHandler;
use Ibexa\VersionComparison\Result\Value\HtmlComparisonResult;

final class HtmlComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\Html\HtmlDiffHandler */
    private $htmlDiffHandler;

    public function __construct(HtmlDiffHandler $htmlDiffHandler)
    {
        $this->htmlDiffHandler = $htmlDiffHandler;
    }

    public function compareValues(
        HtmlComparisonValue $htmlA,
        HtmlComparisonValue $htmlB
    ): HtmlComparisonResult {
        $outputHtml = $this->htmlDiffHandler->getHtmlDiff(
            $htmlA->value,
            $htmlB->value
        );

        return new HtmlComparisonResult(
            $outputHtml,
            $htmlA->value !== $htmlB->value
        );
    }
}

class_alias(HtmlComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\Value\HtmlComparisonEngine');
