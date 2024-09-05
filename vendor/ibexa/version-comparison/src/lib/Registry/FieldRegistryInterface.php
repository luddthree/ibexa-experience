<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Registry;

use Ibexa\Contracts\VersionComparison\FieldType\Comparable;

interface FieldRegistryInterface
{
    public function registerType(string $name, Comparable $type): void;

    public function getType(string $name): Comparable;
}

class_alias(FieldRegistryInterface::class, 'EzSystems\EzPlatformVersionComparison\Registry\FieldRegistryInterface');
