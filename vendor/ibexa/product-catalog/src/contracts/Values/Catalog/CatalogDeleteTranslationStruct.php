<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Catalog;

use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;

final class CatalogDeleteTranslationStruct
{
    private CatalogInterface $catalog;

    private string $languageCode;

    public function __construct(CatalogInterface $catalog, string $languageCode)
    {
        $this->catalog = $catalog;
        $this->languageCode = $languageCode;
    }

    public function getCatalog(): CatalogInterface
    {
        return $this->catalog;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }
}
