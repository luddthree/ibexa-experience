<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings;

use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

abstract class AbstractProductTypeSettingTestCase extends IbexaKernelTestCase
{
    public const MAIN_LANGUAGE_CODE = 'eng-GB';
    public const VIRTUAL_PRODUCT_TYPE_IDENTIFIER = 'some_virtual_product_type';

    protected function setUp(): void
    {
        parent::setUp();

        self::setAdministratorUser();
    }

    protected function getProductSpecificationFieldDefinitionId(ProductTypeInterface $productType): int
    {
        $contentType = self::getContentTypeService()->loadContentTypeByIdentifier($productType->getIdentifier());
        $productSpecification = $contentType->getFirstFieldDefinitionOfType(Type::FIELD_TYPE_IDENTIFIER);

        self::assertNotNull($productSpecification);

        return $productSpecification->id;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function createProductType(
        string $identifier,
        bool $isVirtual = false,
        string $mainLanguageCode = self::MAIN_LANGUAGE_CODE
    ): ProductTypeInterface {
        $productTypeService = self::getLocalProductTypeService();

        $createStruct = $productTypeService->newProductTypeCreateStruct(
            $identifier,
            $mainLanguageCode
        );
        $createStruct->setVirtual($isVirtual);

        return $productTypeService->createProductType($createStruct);
    }
}
