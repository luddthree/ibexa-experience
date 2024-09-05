<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class ProductTypeDeleteData
{
    /**
     * @Assert\NotBlank()
     */
    private ?ProductTypeInterface $productType;

    public function __construct(?ProductTypeInterface $productType = null)
    {
        $this->productType = $productType;
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
