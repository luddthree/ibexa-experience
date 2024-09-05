<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Registry;

use Ibexa\Contracts\VersionComparison\Engine\FieldTypeComparisonEngine;

interface ComparisonEngineRegistryInterface
{
    public function registerEngine(string $supportedType, FieldTypeComparisonEngine $engine): void;

    public function getEngine(string $supportedType): FieldTypeComparisonEngine;
}

class_alias(ComparisonEngineRegistryInterface::class, 'EzSystems\EzPlatformVersionComparison\Registry\ComparisonEngineRegistryInterface');
