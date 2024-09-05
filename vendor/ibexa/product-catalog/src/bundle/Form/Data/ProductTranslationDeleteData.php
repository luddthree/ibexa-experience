<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\AtLeastOneLanguageWillRemain;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;

/**
 * @AtLeastOneLanguageWillRemain
 */
final class ProductTranslationDeleteData extends AbstractTranslationDeleteData
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface */
    private ProductInterface $product;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $product
     * @param array<string, false>|null $languageCodes
     */
    public function __construct(
        ProductInterface $product,
        ?array $languageCodes = []
    ) {
        parent::__construct($languageCodes);

        $this->product = $product;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\ProductInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface
     */
    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getTranslatable(): ?TranslatableInterface
    {
        return $this->product;
    }
}
