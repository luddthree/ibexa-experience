<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\Form\Data;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;

final class VersionComparisonData
{
    private ?Language $language;

    private VersionData $versionA;

    private VersionData $versionB;

    public function __construct(
        VersionInfo $versionA,
        string $versionNoALanguage,
        ?VersionInfo $versionB,
        ?string $versionNoBLanguage,
        ?Language $language = null
    ) {
        $this->language = $language;
        $this->versionA = new VersionData($versionA, $versionNoALanguage);
        $this->versionB = new VersionData($versionB, $versionNoBLanguage);
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function getVersionA(): VersionData
    {
        return $this->versionA;
    }

    public function getVersionB(): VersionData
    {
        return $this->versionB;
    }
}

class_alias(VersionComparisonData::class, 'EzSystems\EzPlatformVersionComparisonBundle\Form\Data\VersionComparisonData');
