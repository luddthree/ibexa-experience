<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Templating;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;

interface ComparisonResultRendererInterface
{
    public function renderContentFieldComparisonResult(
        Content $content,
        FieldDefinition $fieldDefinition,
        ComparisonResult $comparisonResult,
        array $parameters = []
    );
}

class_alias(ComparisonResultRendererInterface::class, 'EzSystems\EzPlatformVersionComparison\Templating\ComparisonResultRendererInterface');
