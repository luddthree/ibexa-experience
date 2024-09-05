<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\ProductCatalog\Local\Repository\ProductSpecificationLocator;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

final class ProductSpecificationLocatorTest extends IbexaKernelTestCase
{
    private ProductSpecificationLocator $locator;

    protected function setUp(): void
    {
        self::setAdministratorUser();

        $this->locator = self::getServiceByClassName(ProductSpecificationLocator::class);
    }

    /**
     * @dataProvider dataProviderForTestFindField
     */
    public function testFindField(
        string $productCode,
        string $specificationFieldIdentifier,
        string $languageCode
    ): void {
        self::getLanguageResolver()->setContextLanguage($languageCode);
        $languageSettings = new LanguageSettings([$languageCode]);
        $product = self::getProductService()->getProduct($productCode, $languageSettings);
        self::assertInstanceOf(ContentAwareProductInterface::class, $product);
        $content = self::getContentService()->loadContent($product->getContent()->id, Language::ALL);

        $field = $this->locator->findField($product);

        self::assertSame(
            $specificationFieldIdentifier,
            $field->fieldTypeIdentifier,
        );

        self::assertSame(
            $content->getVersionInfo()->getContentInfo()->getMainLanguageCode(),
            $field->getLanguageCode(),
        );
    }

    /**
     * @return iterable<array{string, string, string}>
     */
    public function dataProviderForTestFindField(): iterable
    {
        yield [
            '0001',
            'ibexa_product_specification',
            'ger-DE',
        ];

        yield [
            '0002',
            'ibexa_product_specification',
            'eng-GB',
        ];

        yield [
            '0003',
            'ibexa_product_specification',
            'ger-DE',
        ];
    }

    /**
     * @dataProvider dataProviderForTestFindFieldDefinition
     */
    public function testFindFieldDefinition(
        string $productTypeIdentifier,
        string $specificationFieldIdentifier
    ): void {
        $productType = self::getProductTypeService()->getProductType($productTypeIdentifier);

        self::assertSame(
            $specificationFieldIdentifier,
            $this->locator->findFieldDefinition($productType)->fieldTypeIdentifier
        );
    }

    /**
     * @return iterable<array{string, string}>
     */
    public function dataProviderForTestFindFieldDefinition(): iterable
    {
        yield [
            'jeans',
            'ibexa_product_specification',
        ];

        yield [
            'dress',
            'ibexa_product_specification',
        ];
    }
}
