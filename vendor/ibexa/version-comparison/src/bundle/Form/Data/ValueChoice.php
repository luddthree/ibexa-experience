<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\Form\Data;

use Stringable;

final class ValueChoice implements Stringable
{
    private const SEPARATOR = '::';

    private int $versionNo;

    private string $languageCode;

    public function __construct(int $versionNo, string $languageCode)
    {
        $this->versionNo = $versionNo;
        $this->languageCode = $languageCode;
    }

    public static function fromString(string $value): self
    {
        [$versionNo, $languageCode] = explode(self::SEPARATOR, $value);

        return new self((int) $versionNo, $languageCode);
    }

    public function __toString(): string
    {
        return $this->versionNo . self::SEPARATOR . $this->languageCode;
    }

    public function getVersionNo(): int
    {
        return $this->versionNo;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }
}
