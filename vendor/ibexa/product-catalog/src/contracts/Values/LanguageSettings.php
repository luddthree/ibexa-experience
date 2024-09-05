<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;

final class LanguageSettings
{
    /** @var string[] */
    private array $languages;

    private bool $useAlwaysAvailable;

    /**
     * @param string[] $languages
     */
    public function __construct(array $languages = Language::ALL, bool $useAlwaysAvailable = true)
    {
        $this->languages = $languages;
        $this->useAlwaysAvailable = $useAlwaysAvailable;
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    public function getUseAlwaysAvailable(): bool
    {
        return $this->useAlwaysAvailable;
    }
}
