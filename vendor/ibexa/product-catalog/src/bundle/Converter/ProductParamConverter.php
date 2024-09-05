<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Converter;

use Ibexa\Bundle\Core\Converter\RepositoryParamConverter;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class ProductParamConverter extends RepositoryParamConverter
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    protected function getSupportedClass(): string
    {
        return ProductInterface::class;
    }

    protected function getPropertyName(): string
    {
        return 'productCode';
    }

    /**
     * @param string $id Product Code
     */
    protected function loadValueObject($id): ProductInterface
    {
        return $this->productService->getProduct($id);
    }
}
