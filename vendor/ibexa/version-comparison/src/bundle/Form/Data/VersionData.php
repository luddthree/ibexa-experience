<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\Form\Data;

use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;

final class VersionData
{
    private ?string $value;

    private ?VersionInfo $versionInfo;

    private ?string $languageCode;

    public function __construct(?VersionInfo $versionInfo, ?string $languageCode)
    {
        $this->value = $versionInfo ? (string) (new ValueChoice($versionInfo->versionNo, $languageCode)) : null;
        $this->versionInfo = $versionInfo;
        $this->languageCode = $languageCode;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getVersionInfo(): ?VersionInfo
    {
        return $this->versionInfo;
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }
}
