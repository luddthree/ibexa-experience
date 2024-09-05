<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SystemInfo\VersionStability;

use Ibexa\SystemInfo\Value\Stability;

/**
 * @internal
 */
final class ComposerVersionStabilityChecker implements VersionStabilityChecker
{
    public function getStability(string $version): string
    {
        if ($this->isStableVersion($version)) {
            return Stability::STABILITIES[0];
        }

        $stability = $this->getStabilityFromVersionString($version);

        return in_array($stability, Stability::STABILITIES, true)
            ? $stability
            : Stability::STABILITIES[20];
    }

    public function isStableVersion(string $version): bool
    {
        $pattern = '/^(\d+\.\d+\.\d+\.\d+)$/';

        return (bool) preg_match($pattern, $version);
    }

    private function getStabilityFromVersionString(string $version): ?string
    {
        return preg_match('/^(dev)-/', $version, $matches)
        || preg_match('/-(\w+)\d+$/U', $version, $matches)
            ? $matches[1]
            : null;
    }
}

class_alias(ComposerVersionStabilityChecker::class, 'EzSystems\EzSupportTools\VersionStability\ComposerVersionStabilityChecker');
