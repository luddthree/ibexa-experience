<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\Translation;

use Ibexa\Bundle\ProductCatalog\Form\Data\AbstractTranslationDeleteData;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\AtLeastOneLanguageWillRemain;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;

/**
 * @AtLeastOneLanguageWillRemain
 */
final class TranslationDeleteData extends AbstractTranslationDeleteData
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface */
    private CatalogInterface $catalog;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $catalog
     * @param array<string, false>|null $languageCodes
     */
    public function __construct(
        CatalogInterface $catalog,
        ?array $languageCodes = []
    ) {
        parent::__construct($languageCodes);

        $this->catalog = $catalog;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface
     */
    public function getCatalog(): CatalogInterface
    {
        return $this->catalog;
    }

    public function getTranslatable(): ?TranslatableInterface
    {
        return $this->catalog;
    }
}
