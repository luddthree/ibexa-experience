<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SystemInfo\VersionStability;

interface VersionStabilityChecker
{
    public function getStability(string $version): string;

    public function isStableVersion(string $version): bool;
}

class_alias(VersionStabilityChecker::class, 'EzSystems\EzSupportTools\VersionStability\VersionStabilityChecker');
