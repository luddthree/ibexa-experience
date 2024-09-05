<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local;

use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeUpdateStruct;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;

interface LocalProductTypeServiceInterface extends ProductTypeServiceInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createProductType(ProductTypeCreateStruct $createStruct): ProductTypeInterface;

    public function newProductTypeCreateStruct(string $identifier, string $mainLanguageCode): ProductTypeCreateStruct;

    public function newProductTypeUpdateStruct(ProductTypeInterface $productType): ProductTypeUpdateStruct;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function updateProductType(ProductTypeUpdateStruct $updateStruct): ProductTypeInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteProductType(ProductTypeInterface $productType): void;

    public function isProductTypeUsed(ProductTypeInterface $productType): bool;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    public function deleteProductTypeTranslation(
        ProductTypeInterface $productType,
        string $languageCode
    ): void;
}
