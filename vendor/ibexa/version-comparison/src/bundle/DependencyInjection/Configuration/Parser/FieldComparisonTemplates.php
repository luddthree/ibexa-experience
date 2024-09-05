<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\Templates;

class FieldComparisonTemplates extends Templates
{
    public const NODE_KEY = 'field_comparison_templates';
    public const INFO = 'Settings for field comparison templates';
    public const INFO_TEMPLATE_KEY = 'Template file where to find block definition to display field comparison';
}

class_alias(FieldComparisonTemplates::class, 'EzSystems\EzPlatformVersionComparisonBundle\DependencyInjection\Configuration\Parser\FieldComparisonTemplates');
