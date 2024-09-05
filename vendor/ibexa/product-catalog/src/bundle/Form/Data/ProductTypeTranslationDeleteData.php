<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductTypeTranslationDeleteData
{
    /**
     * @var array<string, boolean>|null
     *
     * @Assert\NotBlank()
     */
    private ?array $languageCodes;

    /**
     * @param array<string, boolean>|null $languageCodes
     */
    public function __construct(?array $languageCodes = null)
    {
        $this->languageCodes = $languageCodes;
    }

    /**
     * @return array<string, boolean>|null
     */
    public function getLanguageCodes(): ?array
    {
        return $this->languageCodes;
    }

    /**
     * @param array<string, boolean>|null $languageCodes
     */
    public function setLanguageCodes(?array $languageCodes): void
    {
        $this->languageCodes = $languageCodes;
    }
}
