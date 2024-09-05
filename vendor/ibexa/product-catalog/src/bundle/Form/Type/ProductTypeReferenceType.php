<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductTypeTransformer;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductTypeReferenceType extends AbstractType
{
    private ProductTypeServiceInterface $productTypeService;

    public function __construct(ProductTypeServiceInterface $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new ProductTypeTransformer($this->productTypeService));
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }
}
