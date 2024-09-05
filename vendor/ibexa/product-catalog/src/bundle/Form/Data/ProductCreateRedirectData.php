<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class ProductCreateRedirectData
{
    /**
     * @Assert\NotBlank()
     */
    private ?Language $language;

    /**
     * @Assert\NotBlank()
     */
    private ?ProductTypeInterface $productType;

    public function __construct(?Language $language = null, ?ProductTypeInterface $productType = null)
    {
        $this->language = $language;
        $this->productType = $productType;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): void
    {
        $this->language = $language;
    }

    public function getProductType(): ?ProductTypeInterface
    {
        return $this->productType;
    }

    public function setProductType(?ProductTypeInterface $productType): void
    {
        $this->productType = $productType;
    }
}
