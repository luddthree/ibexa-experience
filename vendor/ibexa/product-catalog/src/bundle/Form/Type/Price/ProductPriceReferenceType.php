<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Price;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductPriceTransformer;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductPriceReferenceType extends AbstractType
{
    private ProductPriceServiceInterface $productPriceService;

    public function __construct(ProductPriceServiceInterface $productPriceService)
    {
        $this->productPriceService = $productPriceService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new ProductPriceTransformer($this->productPriceService));
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }
}
