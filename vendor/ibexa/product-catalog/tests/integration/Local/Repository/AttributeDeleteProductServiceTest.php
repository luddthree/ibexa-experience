<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionUpdateStruct;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

final class AttributeDeleteProductServiceTest extends IbexaKernelTestCase
{
    private const PRODUCT_DELETE_ATTRIBUTE_CODE = 'product_delete_attribute_test_0001';

    protected function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();
        $this->executeMigration('product_delete_attribute_setup.yaml');
    }

    protected function tearDown(): void
    {
        $this->executeMigration('product_delete_attribute_teardown.yaml');
    }

    public function testDeletingAttributeFromProductType(): void
    {
        $productService = self::getProductService();
        $contentTypeService = self::getContentTypeService();
        $product = $productService->getProduct(self::PRODUCT_DELETE_ATTRIBUTE_CODE);

        self::assertCount(2, $product->getAttributes());

        $productContentType = $contentTypeService->loadContentTypeByIdentifier('product_attribute_delete_test');

        $contentTypeDraft = $contentTypeService->createContentTypeDraft($productContentType);

        $specificationFieldDefinition = $productContentType->getFieldDefinition('specification');
        self::assertNotNull($specificationFieldDefinition);

        $contentTypeService->updateFieldDefinition(
            $contentTypeDraft,
            $specificationFieldDefinition,
            new FieldDefinitionUpdateStruct([
                'fieldSettings' => [
                    'attributes_definitions' => [
                        'dimensions' => [
                            [
                                'attributeDefinition' => 'foo',
                                'required' => false,
                                'discriminator' => false,
                            ],
                        ],
                    ],
                    'regions' => [
                        [
                            'region_identifier' => '__REGION_1__',
                            'vat_category_identifier' => 'fii',
                        ],
                    ],
                ],
            ])
        );

        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $product = $productService->getProduct(self::PRODUCT_DELETE_ATTRIBUTE_CODE);

        self::assertCount(1, $product->getAttributes());
    }
}
