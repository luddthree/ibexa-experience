<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;

abstract class AbstractTranslationDeleteData
{
    /** @var array<string, false>|null */
    protected ?array $languageCodes;

    /**
     * @param array<string, false>|null $languageCodes
     */
    public function __construct(?array $languageCodes = null)
    {
        $this->languageCodes = $languageCodes;
    }

    abstract public function getTranslatable(): ?TranslatableInterface;

    /**
     * @return array<string, false>|null
     */
    public function getLanguageCodes(): ?array
    {
        return $this->languageCodes;
    }

    /**
     * @param array<string, false>|null $languageCodes
     */
    public function setLanguageCodes(?array $languageCodes): void
    {
        $this->languageCodes = $languageCodes;
    }
}
