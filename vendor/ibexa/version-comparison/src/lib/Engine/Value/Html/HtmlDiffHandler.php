<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\Value\Html;

interface HtmlDiffHandler
{
    public function getHtmlDiff(string $htmlA, string $htmlB): string;
}

class_alias(HtmlDiffHandler::class, 'EzSystems\EzPlatformVersionComparison\Engine\Value\Html\HtmlDiffHandler');
