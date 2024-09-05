<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct;
use Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\ProductTypeService
 *
 * @group product-type-service
 */
final class ProductTypeServiceTest extends BaseProductTypeServiceTest
{
    private const TOTAL_PRODUCT_TYPE_COUNT = 11;

    protected function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();
    }

    public function testCreatePhysicalProductType(): void
    {
        $productTypeService = self::getLocalProductTypeService();
        $attributeDefinitionService = self::getAttributeDefinitionService();

        self::assertCountProductTypesInDatabaseTable(self::TOTAL_PRODUCT_TYPE_COUNT);

        $createStruct = $productTypeService->newProductTypeCreateStruct('hat', 'eng-GB');
        $createStruct->setAssignedAttributesDefinitions([
            new AssignAttributeDefinitionStruct(
                $attributeDefinitionService->getAttributeDefinition('baz'),
                true,
                false
            ),
            new AssignAttributeDefinitionStruct(
                $attributeDefinitionService->getAttributeDefinition('foobar'),
                false,
                false
            ),
        ]);

        $productType = $productTypeService->createProductType($createStruct);
        $expectedAttributeDefinitionStructs = [
            [
                'attributeDefinition' => 'baz',
                'required' => true,
                'discriminator' => false,
            ],
            [
                'attributeDefinition' => 'foobar',
                'required' => false,
                'discriminator' => false,
            ],
        ];

        self::assertInstanceOf(ContentTypeAwareProductTypeInterface::class, $productType);
        self::assertAttributeDefinitions($expectedAttributeDefinitionStructs, $productType);
        self::assertCountProductTypesInDatabaseTable(self::TOTAL_PRODUCT_TYPE_COUNT + 1);
        $this->assertIsVirtual(false, $productType);
    }

    public function testCreateVirtualProductType(): void
    {
        $productTypeService = self::getLocalProductTypeService();

        self::assertCountProductTypesInDatabaseTable(self::TOTAL_PRODUCT_TYPE_COUNT);

        $createStruct = $productTypeService->newProductTypeCreateStruct('additional_extra_warranty', 'eng-GB');
        $createStruct->setVirtual(true);

        $productType = $productTypeService->createProductType($createStruct);

        self::assertInstanceOf(ContentTypeAwareProductTypeInterface::class, $productType);
        self::assertCountProductTypesInDatabaseTable(self::TOTAL_PRODUCT_TYPE_COUNT + 1);
        $this->assertIsVirtual(true, $productType);
    }

    public function testUpdateProductType(): void
    {
        $productTypeService = self::getProductTypeService();
        $localProductTypeService = self::getLocalProductTypeService();
        $contentTypeService = self::getContentTypeService();
        $attributeDefinitionService = self::getAttributeDefinitionService();

        self::ensureSearchIndexIsUpdated();
        self::assertCountProductTypesInDatabaseTable(self::TOTAL_PRODUCT_TYPE_COUNT);

        $productType = $productTypeService->getProductType('skirt');
        $updateStruct = $localProductTypeService->newProductTypeUpdateStruct($productType);
        $contentTypeUpdateStruct = $contentTypeService->newContentTypeUpdateStruct();
        $contentTypeUpdateStruct->names = [
            'eng-GB' => 'updated_name',
        ];

        $updateStruct->setContentTypeUpdateStruct($contentTypeUpdateStruct);

        $updateStruct->addAttributeDefinition(new AssignAttributeDefinitionStruct(
            $attributeDefinitionService->getAttributeDefinition('foobar'),
            true,
            true
        ));
        $updateStruct->setVirtual(true);

        $productType = $localProductTypeService->updateProductType($updateStruct);

        $expectedAttributeDefinitionStructs = [
            [
                'attributeDefinition' => 'foo',
                'required' => true,
                'discriminator' => false,
            ],
            [
                'attributeDefinition' => 'bar',
                'required' => false,
                'discriminator' => false,
            ],
            [
                'attributeDefinition' => 'baz',
                'required' => true,
                'discriminator' => false,
            ],
            [
                'attributeDefinition' => 'foobar',
                'required' => true,
                'discriminator' => true,
            ],
        ];

        self::assertInstanceOf(ContentTypeAwareProductTypeInterface::class, $productType);
        self::assertSame('updated_name', $productType->getName());
        self::assertAttributeDefinitions($expectedAttributeDefinitionStructs, $productType);
        $this->assertIsVirtual(true, $productType);

        self::ensureSearchIndexIsUpdated();
        self::assertCountProductTypesInDatabaseTable(self::TOTAL_PRODUCT_TYPE_COUNT);
    }

    public function testGetProductType(): void
    {
        $productType = self::getProductTypeService()->getProductType('trousers');

        self::assertInstanceOf(ProductTypeInterface::class, $productType);
        self::assertEquals('trousers', $productType->getIdentifier());
        self::assertEquals('Trousers', $productType->getName());

        $assignments = $productType->getAttributesDefinitions();

        $expectedIdentifiers = ['foo', 'bar', 'baz'];
        $this->assertHasAttributeDefinitions($expectedIdentifiers, $assignments);
        $this->assertHasRequiredAttributeDefinitions(['foo', 'baz'], $assignments);
        $this->assertAttributeDefinitionsNames(['Foo', 'Bar', 'Baz'], $assignments);
        $this->assertHasDiscriminatorAttributeDefinitions(['bar', 'baz'], $assignments);
        $this->assertIsVirtual(false, $productType);
    }

    public function testGetProductTypeWithForcedLanguageSettings(): void
    {
        $productType = self::getProductTypeService()->getProductType(
            'dress',
            new LanguageSettings(['ger-DE'])
        );

        self::assertInstanceOf(ProductTypeInterface::class, $productType);
        self::assertEquals('dress', $productType->getIdentifier());
        self::assertEquals('Dress (DE)', $productType->getName());

        $assignments = $productType->getAttributesDefinitions();

        $this->assertHasAttributeDefinitions(['foo', 'bar', 'baz'], $assignments);
        $this->assertAttributeDefinitionsNames(['Foo_de', 'Bar_de', 'Baz_de'], $assignments);
        $this->assertHasRequiredAttributeDefinitions(['foo', 'baz'], $assignments);
    }

    public function testGetProductTypeThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\ProductCatalog\Local\Repository\Values\ProductType' with identifier 'non-existing'");

        self::getProductTypeService()->getProductType('non-existing');
    }

    public function testFindProductTypes(): void
    {
        $productTypesList = self::getProductTypeService()->findProductTypes(new ProductTypeQuery());

        self::assertEquals(self::TOTAL_PRODUCT_TYPE_COUNT, $productTypesList->getTotalCount());
        $this->assertProductTypeListItems([
            'attribute_search_check',
            'blouse',
            'dress',
            'empty',
            'jeans',
            'shirt',
            'skirt',
            'socks',
            'sweater',
            'trousers',
            'warranty',
        ], $productTypesList);
    }

    /**
     * @dataProvider dataProviderForProductTypeListWithQuery
     *
     * @param array<int, string> $expectedList
     */
    public function testGetProductTypeListWithQuery(
        ProductTypeQuery $query,
        int $expectedTotalCount,
        array $expectedList
    ): void {
        $productTypesList = self::getProductTypeService()->findProductTypes($query);

        self::assertSame($expectedTotalCount, $productTypesList->getTotalCount());
        $this->assertProductTypeListItems($expectedList, $productTypesList);
    }

    /**
     * @return iterable<array{\Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery, int, string[]}>
     */
    public function dataProviderForProductTypeListWithQuery(): iterable
    {
        yield 'filter by prefix' => [
            new ProductTypeQuery('S'),
            4,
            ['shirt', 'skirt', 'socks', 'sweater'],
        ];

        yield 'filter by prefix (case insensitivity)' => [
            new ProductTypeQuery('s'),
            4,
            ['shirt', 'skirt', 'socks', 'sweater'],
        ];

        yield 'pagination' => [
            new ProductTypeQuery(null, 1, 1),
            self::TOTAL_PRODUCT_TYPE_COUNT,
            ['blouse'],
        ];

        yield 'filter and pagination' => [
            new ProductTypeQuery('S', 1, 1),
            4,
            ['skirt'],
        ];
    }

    public function testDeleteProductTypeWithItems(): void
    {
        $productTypeService = self::getLocalProductTypeService();

        $productType = $productTypeService->getProductType('dress');

        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage("Argument '\$productTypeId' has a bad state: Product Type with the given ID still has Product items and cannot be deleted");

        $productTypeService->deleteProductType($productType);
    }

    public function testDeleteProductType(): void
    {
        $productTypeService = self::getLocalProductTypeService();

        $productType = $productTypeService->getProductType('empty');

        $productTypeService->deleteProductType($productType);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\ProductCatalog\Local\Repository\Values\ProductType' with identifier 'empty'");

        $productTypeService->getProductType('empty');
    }

    public function testDeleteProductTypeTranslation(): void
    {
        $productTypeService = self::getLocalProductTypeService();

        $createStruct = $productTypeService->newProductTypeCreateStruct('translatable', 'eng-GB');
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface $productType */
        $productType = $productTypeService->createProductType($createStruct);

        $updateStruct = $productTypeService->newProductTypeUpdateStruct($productType);
        $updateStruct->getContentTypeUpdateStruct()->names = [
            'eng-GB' => 'Name (EN)',
            'ger-DE' => 'Name (DE)',
            'pol-PL' => 'Name (PL)',
            'fre-FR' => 'Name (FR)',
        ];

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface $productType */
        $productType = $productTypeService->updateProductType($updateStruct);

        self::assertEquals(
            ['ger-DE', 'eng-GB', 'pol-PL', 'fre-FR'],
            $productType->getContentType()->languageCodes
        );

        $productTypeService->deleteProductTypeTranslation($productType, 'ger-DE');

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface $productType */
        $productType = $productTypeService->getProductType('translatable');

        self::assertEquals(
            ['eng-GB', 'pol-PL', 'fre-FR'],
            $productType->getContentType()->languageCodes
        );
    }
}
