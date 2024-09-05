<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Converter;

use Ibexa\Bundle\Core\Converter\RepositoryParamConverter;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;

final class ProductTypeParamConverter extends RepositoryParamConverter
{
    private ProductTypeServiceInterface $productTypeService;

    public function __construct(ProductTypeServiceInterface $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }

    protected function getSupportedClass(): string
    {
        return ProductTypeInterface::class;
    }

    protected function getPropertyName(): string
    {
        return 'productTypeIdentifier';
    }

    /**
     * @param string $id
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function loadValueObject($id): ProductTypeInterface
    {
        return $this->productTypeService->getProductType($id);
    }
}
