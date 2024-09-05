<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class ProductDeleteData
{
    /**
     * @Assert\NotBlank()
     */
    private ?ProductInterface $product;

    public function __construct(?ProductInterface $product = null)
    {
        $this->product = $product;
    }

    public function getProduct(): ?ProductInterface
    {
        return $this->product;
    }

    public function setProduct(?ProductInterface $product): void
    {
        $this->product = $product;
    }
}
