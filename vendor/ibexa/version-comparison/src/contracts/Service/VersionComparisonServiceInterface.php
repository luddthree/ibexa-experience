<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\VersionComparison\Service;

use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\VersionComparison\VersionDiff;

interface VersionComparisonServiceInterface
{
    /**
     * Calculate difference between data in fieldTypes in given Versions.
     *
     * Fields should implement \Ibexa\Contracts\Core\FieldType\Comparable
     * and be registered with `ibexa.field_type.comparable` tag to get proper data to ComparisonEngine.
     *
     * Engines should implement \Ibexa\Core\Comparison\ComparisonEngine
     * and be registered with `ibexa.field_type.comparable.engine`.
     *
     * Only Versions in same language can be compared.
     *
     * @param string|null $languageCode if not provided, initialLanguageCode of $versionA is used.
     */
    public function compare(
        VersionInfo $versionA,
        VersionInfo $versionB,
        ?string $languageCode = null
    ): VersionDiff;
}

class_alias(VersionComparisonServiceInterface::class, 'EzSystems\EzPlatformVersionComparison\Service\VersionComparisonServiceInterface');
