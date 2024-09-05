<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Range as RangeAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResultEntry;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\StatsAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductList\AggregationResult\PriceStatsAggregationResult;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AttributeBooleanTermAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AttributeColorTermAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AttributeFloatRangeAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AttributeFloatStatsAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AttributeIntegerRangeAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AttributeIntegerStatsAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AttributeSelectionTermAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\BasePriceStatsAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\CustomPriceStatsAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\ProductAvailabilityTermAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\ProductPriceRangeAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\ProductStockRangeAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\ProductTypeTermAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CheckboxAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ColorAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IntegerAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalOr;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductName;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductStock;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductStockRange;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductType;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\SelectionAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductList;
use Money\Currency;
use Money\Money;
use Traversable;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\ProductService
 *
 * @group product-service
 */
final class ProductServiceTest extends BaseProductServiceTest
{
    private const NON_PRODUCT_CONTENT_ID = 52;
    private const PRODUCT_WITH_VARIANTS_CODE = 'TROUSERS_0001';
    private const COLOR_WHITE = '#FFFFFF';
    private const COLOR_GREY = '#999999';

    public function testCreateProduct(): void
    {
        $productService = self::getLocalProductService();

        $count = self::getProductsCount();

        $productCreateStruct = $productService->newProductCreateStruct(
            self::getProductTypeService()->getProductType(self::TEST_PRODUCT_TYPE_IDENTIFIER_TROUSERS),
            'eng-GB'
        );

        self::assertInstanceOf(
            ProductCreateStruct::class,
            $productCreateStruct,
            'Local implementation of ProductService returns a specific concrete ' . ProductCreateStruct::class
        );
        $productCreateStruct->setCode('code');
        $productCreateStruct->setField('name', 'foo');
        $productCreateStruct->setAttributes([
            'foo' => 9,
        ]);

        try {
            $productService->createProduct($productCreateStruct);
            self::fail('Validation should not pass due to "foo" attribute being lower than 10.');
        } catch (InvalidArgumentException $e) {
            self::assertSame('Argument \'$createStruct\' is invalid: \'9\' is incorrect value', $e->getMessage());
        }

        $productCreateStruct->setAttribute('foo', 10);

        $product = $productService->createProduct($productCreateStruct);

        self::assertInstanceOf(ContentAwareProductInterface::class, $product);
        self::assertSame('code', $product->getCode());
        self::assertSame('foo', $product->getName());
        self::assertAttributesValue([
            'foo' => 10,
        ], $product);

        self::assertCountProductsInDatabaseTable($count + 1);
        self::assertVersionNoIsSameInProductStorageForProductCode(
            $product->getContent()->versionInfo->versionNo,
            $product->getCode()
        );
    }

    /**
     * @dataProvider dataProviderForInvalidProductCode
     */
    public function testCreateProductValidateProductCode(string $code, string $expectedError): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$createStruct\' is invalid: ' . $expectedError);

        $productService = self::getLocalProductService();

        $createStruct = $productService->newProductCreateStruct(
            self::getProductTypeService()->getProductType(self::TEST_PRODUCT_TYPE_IDENTIFIER_TROUSERS),
            'eng-GB'
        );
        $createStruct->setCode($code);

        $productService->createProduct($createStruct);
    }

    public function testGetProduct(): void
    {
        $product = self::getProductService()->getProduct(self::PRODUCT_WITH_VARIANTS_CODE);
        self::assertSame(self::PRODUCT_WITH_VARIANTS_CODE, $product->getCode());
    }

    public function testGetProductFromContent(): void
    {
        $product = self::getLocalProductService()->getProduct(self::PRODUCT_WITH_VARIANTS_CODE);
        self::assertInstanceOf(ContentAwareProductInterface::class, $product);

        $contentService = self::getContentService();
        $productService = self::getLocalProductService();

        $contentWithProductSpecification = $contentService->loadContent($product->getContent()->id);

        self::assertSame(
            $product->getCode(),
            $productService->getProductFromContent($contentWithProductSpecification)->getCode()
        );
    }

    public function testIsProduct(): void
    {
        $productService = self::getLocalProductService();
        $product = $productService->getProduct(self::PRODUCT_WITH_VARIANTS_CODE);
        self::assertInstanceOf(ContentAwareProductInterface::class, $product);

        $contentService = self::getContentService();

        $contentWithoutProductSpecification = $contentService->loadContent(self::NON_PRODUCT_CONTENT_ID);
        $contentWithProductSpecification = $contentService->loadContent($product->getContent()->id);

        self::assertFalse($productService->isProduct($contentWithoutProductSpecification));
        self::assertTrue($productService->isProduct($contentWithProductSpecification));
    }

    /**
     * @modifiesSearchIndex
     */
    public function testUpdateProduct(): void
    {
        $count = self::getProductsCount();
        $productService = self::getLocalProductService();
        $product = $productService->getProduct('0002');
        self::assertInstanceOf(ContentAwareProductInterface::class, $product);
        self::assertAttributesValue([], $product);

        $originalVersionNo = $product->getContent()->getVersionInfo()->versionNo;

        $updateStruct = $productService->newProductUpdateStruct($product);
        $updateStruct->setField('name', 'Different Name');
        $updateStruct->setCode('different-code');
        $updateStruct->setAttribute('foo', 9);

        try {
            $productService->updateProduct($updateStruct);
            self::fail('Validation should not pass due to "foo" attribute being lower than 10.');
        } catch (InvalidArgumentException $e) {
            self::assertSame('Argument \'$updateStruct\' is invalid: \'9\' is incorrect value', $e->getMessage());
        }

        $updateStruct->setAttribute('foo', 20);

        $product = $productService->updateProduct($updateStruct);

        self::assertInstanceOf(ContentAwareProductInterface::class, $product);
        self::assertSame('different-code', $product->getCode());
        self::assertSame('Different Name', $product->getName());
        self::assertAttributesValue([
            'foo' => 20,
        ], $product);

        $newVersionNo = $product->getContent()->getVersionInfo()->versionNo;
        self::assertNotSame($originalVersionNo, $newVersionNo);
        self::assertVersionNoIsSameInProductStorageForProductCode($newVersionNo, $product->getCode());

        self::assertCountProductsInDatabaseTable($count);
    }

    /**
     * @dataProvider dataProviderForInvalidProductCode
     */
    public function testUpdateProductValidateProductCode(string $code, string $expectedError): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$updateStruct\' is invalid: ' . $expectedError);

        $productService = self::getLocalProductService();

        $product = $productService->getProduct('0002');

        $updateStruct = $productService->newProductUpdateStruct($product);
        $updateStruct->setCode($code);

        $productService->updateProduct($updateStruct);
    }

    public function testFindProducts(): void
    {
        $count = self::getContentProductsCount();

        $productService = self::getProductService();
        $productList = $productService->findProducts(new ProductQuery());

        self::assertCount($count, $productList);
        self::assertContainsOnlyInstancesOf(ProductInterface::class, $productList);
    }

    /**
     * @dataProvider provideForTestFindDressProducts
     */
    public function testFindDressProducts(ProductQuery $query): void
    {
        $productService = self::getProductService();
        $productList = $productService->findProducts($query);

        self::assertCount(3, $productList);
        self::assertContainsOnlyInstancesOf(ProductInterface::class, $productList);
        foreach ($productList->getProducts() as $product) {
            self::assertStringStartsWith('Dress', $product->getName());
            self::assertSame('dress', $product->getProductType()->getIdentifier());
        }
    }

    /**
     * @return iterable<string, array{\Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery}>
     */
    public static function provideForTestFindDressProducts(): iterable
    {
        yield 'Name ("Dress*") query' => [
            new ProductQuery(null, new ProductName('Dress*')),
        ];

        yield 'Type ("dress" identifier) query' => [
            new ProductQuery(new ProductType([self::TEST_PRODUCT_TYPE_IDENTIFIER_DRESS])),
        ];
    }

    /**
     * @dataProvider provideForFindProductsWithAggregation
     * @dataProvider provideAttributeAggregationsForFindProductsWithAggregation
     */
    public function testFindProductsWithAggregation(
        ProductQuery $query,
        callable $expectations
    ): void {
        if ($_ENV['SEARCH_ENGINE'] === 'legacy') {
            self::markTestSkipped('Aggregations are unsupported in legacy search engine.');
        }
        $count = self::getContentProductsCount();

        $productService = self::getProductService();
        $productList = $productService->findProducts($query);

        self::assertCount($count, $productList);
        self::assertContainsOnlyInstancesOf(ProductInterface::class, $productList);

        self::assertInstanceOf(ProductList::class, $productList);
        $aggregations = $productList->getAggregations();
        self::assertInstanceOf(AggregationResultCollection::class, $aggregations);
        self::assertCount(count($query->getAggregations()), $aggregations, 'Aggregation count mismatch');
        $expectations($aggregations);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery|\Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery $query
     *
     * @dataProvider dataProviderForFindProductsWithPriceStatsAggregation
     */
    public function testFindProductsWithPriceStatsAggregation(
        $query,
        callable $expectations
    ): void {
        if ($_ENV['SEARCH_ENGINE'] === 'legacy') {
            self::markTestSkipped('Aggregations are unsupported in legacy search engine.');
        }

        self::executeMigration('price_stats_aggregation_setup.yaml');
        self::ensureSearchIndexIsUpdated();

        if (is_callable($query)) {
            $query = $query();
        }

        $aggregations = self::getProductService()->findProducts($query)->getAggregations();

        self::assertInstanceOf(AggregationResultCollection::class, $aggregations);
        self::assertCount(count($query->getAggregations()), $aggregations, 'Aggregation count mismatch');

        $expectations($aggregations);

        self::executeMigration('price_stats_aggregation_teardown.yaml');
        self::ensureSearchIndexIsUpdated();
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery,
     *     callable(
     *         \Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection $aggregations
     *     ): void,
     * }>
     */
    public static function provideForFindProductsWithAggregation(): iterable
    {
        $query = new ProductQuery();
        $query->setAggregations([
            new ProductTypeTermAggregation('test_product_type_aggregation'),
        ]);

        yield 'Product type aggregation' => [
            $query,
            static function (AggregationResultCollection $aggregations): void {
                $aggregation = $aggregations->first();
                self::assertInstanceOf(TermAggregationResult::class, $aggregation);
                self::assertSame('test_product_type_aggregation', $aggregation->getName());

                foreach ($aggregation->getEntries() as $entry) {
                    self::assertInstanceOf(ContentType::class, $entry->getKey());
                    self::assertGreaterThan(0, $entry->getCount());
                }
            },
        ];

        $query = new ProductQuery();
        $query->setAggregations([
            new ProductPriceRangeAggregation('test_base_price_aggregation', 'PLN', [
                new RangeAggregation(0, 10000),
                new RangeAggregation(10000, null),
            ]),
        ]);

        yield 'Base range price aggregation' => [
            $query,
            static function (AggregationResultCollection $aggregations): void {
                $aggregation = $aggregations->first();
                self::assertRangeAggregation(
                    $aggregation,
                    'test_base_price_aggregation',
                    [
                        '0:10000' => 0,
                        '10000:~' => 0,
                    ],
                );
            },
        ];

        $query = new ProductQuery();
        $query->setAggregations([
            new ProductAvailabilityTermAggregation('test_availability_aggregation'),
        ]);

        yield 'Availability aggregation' => [
            $query,
            static function (AggregationResultCollection $aggregations): void {
                $aggregation = $aggregations->first();
                self::assertBooleanAggregation($aggregation, 'test_availability_aggregation', 1, 12);
            },
        ];

        $query = new ProductQuery();
        $query->setAggregations([
            new ProductStockRangeAggregation('test_stock_aggregation', [
                new RangeAggregation(0, 100),
                new RangeAggregation(100, null),
            ]),
        ]);

        yield 'Stock aggregation' => [
            $query,
            static function (AggregationResultCollection $aggregations): void {
                $aggregation = $aggregations->first();
                self::assertRangeAggregation(
                    $aggregation,
                    'test_stock_aggregation',
                    [
                        '0:100' => 1,
                        '100:~' => 0,
                    ],
                );
            },
        ];
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery,
     *     callable(\Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection $aggregations): void,
     * }>
     */
    public static function provideAttributeAggregationsForFindProductsWithAggregation(): iterable
    {
        $query = new ProductQuery();
        $query->setAggregations([
            new AttributeBooleanTermAggregation('foo_boolean_test', 'foo_boolean'),
        ]);

        yield 'Boolean attribute aggregation' => [
            $query,
            static function (AggregationResultCollection $aggregations): void {
                $aggregation = $aggregations->first();
                self::assertBooleanAggregation($aggregation, 'foo_boolean_test', 2, 1);
            },
        ];

        $query = new ProductQuery();
        $query->setAggregations([
            new AttributeSelectionTermAggregation('test_attribute_selection_aggregation', 'foo_selection'),
        ]);

        yield 'Selection attribute aggregation' => [
            $query,
            static function (AggregationResultCollection $aggregations): void {
                $aggregation = $aggregations->first();
                self::assertSelectionAggregation(
                    $aggregation,
                    'test_attribute_selection_aggregation',
                    [
                        'attribute_search_check_selection_1' => 2,
                        'attribute_search_check_selection_2' => 1,
                    ],
                );
            },
        ];

        $query = new ProductQuery();
        $query->setAggregations([
            new AttributeColorTermAggregation('test_attribute_color_aggregation', 'foo_color'),
        ]);

        yield 'Color attribute aggregation' => [
            $query,
            static function (AggregationResultCollection $aggregations): void {
                $aggregation = $aggregations->first();
                self::assertSelectionAggregation(
                    $aggregation,
                    'test_attribute_color_aggregation',
                    [
                        '#999999' => 1,
                        '#ffffff' => 1,
                    ],
                );
            },
        ];

        $query = new ProductQuery();
        $query->setAggregations([
            new AttributeIntegerStatsAggregation('foo_integer_test', 'foo_integer'),
        ]);

        yield 'Integer attribute statistics aggregation' => [
            $query,
            static function (AggregationResultCollection $aggregations): void {
                $aggregation = $aggregations->first();
                self::assertStatsAggregation(
                    $aggregation,
                    'foo_integer_test',
                    1180.0,
                    4,
                    42.0,
                    1000.0,
                    295.0,
                );
            },
        ];

        $query = new ProductQuery();
        $query->setAggregations([
            new AttributeIntegerRangeAggregation(
                'foo_integer_range_test',
                'foo_integer',
                [
                    new RangeAggregation(null, 0),
                    new RangeAggregation(0, 500),
                    new RangeAggregation(500, null),
                ],
            ),
        ]);

        yield 'Integer attribute range aggregation' => [
            $query,
            static function (AggregationResultCollection $aggregations): void {
                $aggregation = $aggregations->first();
                self::assertRangeAggregation(
                    $aggregation,
                    'foo_integer_range_test',
                    [
                        '~:0' => 0,
                        '0:500' => 3,
                        '500:~' => 1,
                    ],
                );
            },
        ];

        $query = new ProductQuery();
        $query->setAggregations([
            new AttributeFloatStatsAggregation('foo_float_test', 'foo_float'),
        ]);

        yield 'Float attribute statistics aggregation' => [
            $query,
            static function (AggregationResultCollection $aggregations): void {
                $aggregation = $aggregations->first();
                self::assertStatsAggregation(
                    $aggregation,
                    'foo_float_test',
                    18.0,
                    3,
                    4.2,
                    9.6,
                    6.0,
                );
            },
        ];

        $query = new ProductQuery();
        $query->setAggregations([
            new AttributeFloatRangeAggregation(
                'foo_float_range_test',
                'foo_float',
                [
                    new RangeAggregation(null, 0),
                    new RangeAggregation(0, 500),
                    new RangeAggregation(500, null),
                ],
            ),
        ]);

        yield 'Float attribute range aggregation' => [
            $query,
            static function (AggregationResultCollection $aggregations): void {
                $aggregation = $aggregations->first();
                self::assertRangeAggregation(
                    $aggregation,
                    'foo_float_range_test',
                    [
                        '~:0.00' => 0,
                        '0.00:500.00' => 3,
                        '500.00:~' => 0,
                    ],
                    static fn (float $value): string => sprintf('%.2f', $value),
                );
            },
        ];
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery|callable(): \Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery,
     *     callable(
     *         \Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection $aggregations
     *     ): void,
     * }>
     */
    public function dataProviderForFindProductsWithPriceStatsAggregation(): iterable
    {
        yield 'Base stats price aggregation' => [
            static function (): ProductQuery {
                $currency = self::getCurrencyService()->getCurrencyByCode('EUR');

                $query = new ProductQuery();
                $query->setAggregations([
                    new BasePriceStatsAggregation(
                        'base_price_stats_aggregation',
                        $currency
                    ),
                ]);

                return $query;
            },
            static function (AggregationResultCollection $aggregations): void {
                self::assertPriceStatsAggregation(
                    $aggregations->first(),
                    'base_price_stats_aggregation',
                    new Money(60000, new Currency('EUR')),
                    3,
                    new Money(10000, new Currency('EUR')),
                    new Money(30000, new Currency('EUR')),
                    new Money(20000, new Currency('EUR')),
                );
            },
        ];

        yield 'Custom stats price aggregation' => [
            static function (): ProductQuery {
                $currency = self::getCurrencyService()->getCurrencyByCode('EUR');
                $customerGroup = self::getCustomerGroupService()->getCustomerGroupByIdentifier('vip');

                $query = new ProductQuery();
                $query->setAggregations([
                    new CustomPriceStatsAggregation(
                        'custom_price_stats_aggregation',
                        $currency,
                        $customerGroup
                    ),
                ]);

                return $query;
            },
            static function (AggregationResultCollection $aggregations): void {
                self::assertPriceStatsAggregation(
                    $aggregations->first(),
                    'custom_price_stats_aggregation',
                    new Money(43000, new Currency('EUR')),
                    3,
                    new Money(6000, new Currency('EUR')),
                    new Money(29000, new Currency('EUR')),
                    new Money(14333, new Currency('EUR')),
                );
            },
        ];
    }

    public function testUpdatingProductWithInvalidAttributeThrowsException(): void
    {
        // Sanity check
        $this->setAttributeInProduct(self::PRODUCT_WITH_VARIANTS_CODE, 'foo', 42);

        $this->expectException(InvalidArgumentValue::class);
        $this->expectExceptionMessage('Argument \'$updateStruct\' is invalid: \'0\' is incorrect value');
        $this->setAttributeInProduct(self::PRODUCT_WITH_VARIANTS_CODE, 'foo', 0);
    }

    public function testCreateProductVariants(): void
    {
        $productVariantCode = 'TROUSERS_0001V1';
        $productService = self::getLocalProductService();
        $productService->createProductVariants(
            $productService->getProduct(self::PRODUCT_WITH_VARIANTS_CODE),
            [
                new ProductVariantCreateStruct(
                    [
                        'bar' => true,
                        'baz' => 10,
                    ],
                    $productVariantCode
                ),
            ]
        );

        $this->assertProductVariant(
            $productService->getProduct($productVariantCode),
            $productVariantCode,
            self::PRODUCT_WITH_VARIANTS_CODE,
            [
                'foo_integer' => 42,
                'bar' => true,
                'baz' => 10,
            ]
        );
    }

    /**
     * @dataProvider dataProviderForInvalidProductCode
     */
    public function testCreateProductVariantsValidateProductCode(string $code, string $expectedError): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument '\$createStruct' is invalid: $expectedError");

        $productService = self::getLocalProductService();
        $productService->createProductVariants(
            $productService->getProduct(self::PRODUCT_WITH_VARIANTS_CODE),
            [
                new ProductVariantCreateStruct(
                    [],
                    $code
                ),
            ]
        );
    }

    /**
     * @return iterable<array{?\Ibexa\Contracts\ProductCatalog\Values\LanguageSettings, ?\Ibexa\Contracts\ProductCatalog\Values\LanguageSettings}>
     */
    public function dataProviderForFindVariants(): iterable
    {
        yield 'default language' => [
            null,
            null,
        ];

        yield 'fetch ger-DE' => [
            null,
            new LanguageSettings(['ger-DE']),
        ];

        yield 'create ger-DE' => [
            new LanguageSettings(['ger-DE']),
            null,
        ];

        yield 'create ger-DE, fetch pol-PO' => [
            new LanguageSettings(['ger-DE']),
            new LanguageSettings(['pol-PO']),
        ];
    }

    /**
     * @dataProvider dataProviderForFindVariants
     */
    public function testFindVariants(?LanguageSettings $createLanguageSettings, ?LanguageSettings $fetchLanguageSettings): void
    {
        $productService = self::getLocalProductService();
        $productCreateVariant = $productService->getProduct(self::PRODUCT_WITH_VARIANTS_CODE, $createLanguageSettings);

        $productService->createProductVariants(
            $productCreateVariant,
            [
                new ProductVariantCreateStruct(
                    [
                        'bar' => true,
                        'baz' => 10,
                    ],
                    'TROUSERS_0001V1'
                ),
                new ProductVariantCreateStruct(
                    [
                        'bar' => true,
                        'baz' => 100,
                    ],
                    'TROUSERS_0001V2'
                ),
                new ProductVariantCreateStruct(
                    [
                        'bar' => true,
                        'baz' => 1000,
                    ],
                    'TROUSERS_0001V3'
                ),
            ]
        );

        $product = $productService->getProduct(self::PRODUCT_WITH_VARIANTS_CODE, $fetchLanguageSettings);
        $actualList = $productService->findProductVariants($product);

        self::assertEquals(3, $actualList->getTotalCount());
        self::assertEquals(
            ['TROUSERS_0001V1', 'TROUSERS_0001V2', 'TROUSERS_0001V3'],
            array_map(
                static fn (ProductInterface $product): string => $product->getCode(),
                $actualList->getVariants()
            )
        );
    }

    public function testUpdateVariant(): void
    {
        $productVariantCode = 'TROUSERS_0001V1';
        $productService = self::getLocalProductService();
        $productService->createProductVariants(
            $productService->getProduct(self::PRODUCT_WITH_VARIANTS_CODE),
            [
                new ProductVariantCreateStruct(
                    [
                        'bar' => true,
                        'baz' => 10,
                    ],
                    $productVariantCode
                ),
            ]
        );

        $this->assertProductVariant(
            $productService->getProduct($productVariantCode),
            $productVariantCode,
            self::PRODUCT_WITH_VARIANTS_CODE,
            [
                'foo_integer' => 42,
                'bar' => true,
                'baz' => 10,
            ]
        );

        $productService->updateProductVariant(
            $productService->getProductVariant($productVariantCode),
            new ProductVariantUpdateStruct([
                'bar' => false,
                'baz' => 1000,
            ])
        );

        $this->assertProductVariant(
            $productService->getProduct($productVariantCode),
            $productVariantCode,
            self::PRODUCT_WITH_VARIANTS_CODE,
            [
                'foo_integer' => 42,
                'bar' => false,
                'baz' => 1000,
            ]
        );
    }

    /**
     * @dataProvider dataProviderForInvalidProductCode
     */
    public function testUpdateVariantValidateProductCode(string $code, string $expectedError): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$updateStruct\' is invalid: ' . $expectedError);

        $productService = self::getLocalProductService();
        $productService->createProductVariants(
            $productService->getProduct(self::PRODUCT_WITH_VARIANTS_CODE),
            [
                new ProductVariantCreateStruct(
                    [
                        'bar' => true,
                        'baz' => 10,
                    ],
                    'TROUSERS_0001V1'
                ),
            ]
        );

        $updateStruct = new ProductVariantUpdateStruct();
        $updateStruct->setCode($code);

        $productService->updateProductVariant(
            $productService->getProductVariant('TROUSERS_0001V1'),
            $updateStruct
        );
    }

    public function testDeleteVariant(): void
    {
        $productVariantCode = 'TROUSERS_0001V1';
        $productService = self::getLocalProductService();
        $productService->createProductVariants(
            $productService->getProduct(self::PRODUCT_WITH_VARIANTS_CODE),
            [
                new ProductVariantCreateStruct(
                    [
                        'bar' => true,
                        'baz' => 10,
                    ],
                    $productVariantCode
                ),
            ]
        );

        $variant = $productService->getProduct($productVariantCode);

        self::assertEquals($productVariantCode, $variant->getCode());

        $productService->deleteProduct($variant);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\ProductCatalog\Local\Persistence\Values\Product' with identifier '$productVariantCode'");

        $productService->getProduct($productVariantCode);
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface,
     *     string[],
     * }>
     */
    public static function provideForAttributeFiltering(): iterable
    {
        yield 'foo_integer = 42' => [
            new IntegerAttribute('foo_integer', 42),
            ['ATTRIBUTE_SEARCH_CHECK_0003', 'TROUSERS_0001'],
        ];

        yield 'foo_boolean = true' => [
            new CheckboxAttribute('foo_boolean', true),
            ['ATTRIBUTE_SEARCH_CHECK_0003', 'ATTRIBUTE_SEARCH_CHECK_0001'],
        ];

        yield 'foo_color = "#FFFFFF"' => [
            new ColorAttribute('foo_color', [self::COLOR_WHITE]),
            ['ATTRIBUTE_SEARCH_CHECK_0003'],
        ];

        yield 'foo_color = "#FFFFFF" || foo_color = "#999999"' => [
            new ColorAttribute('foo_color', [self::COLOR_WHITE, self::COLOR_GREY]),
            ['ATTRIBUTE_SEARCH_CHECK_0003', 'ATTRIBUTE_SEARCH_CHECK_0001'],
        ];

        yield 'foo_boolean = false' => [
            new CheckboxAttribute('foo_boolean', false),
            ['ATTRIBUTE_SEARCH_CHECK_0004'],
        ];

        yield 'foo_boolean = null' => [
            new CheckboxAttribute('foo_boolean', null),
            ['ATTRIBUTE_SEARCH_CHECK_0002'],
        ];

        yield 'foo_integer = null' => [
            new IntegerAttribute('foo_integer', null),
            ['ATTRIBUTE_SEARCH_CHECK_0002'],
        ];

        yield 'foo_float = null' => [
            new FloatAttribute('foo_float', null),
            ['ATTRIBUTE_SEARCH_CHECK_0002'],
        ];

        yield 'foo_selection = null' => [
            new SelectionAttribute('foo_selection', null),
            ['ATTRIBUTE_SEARCH_CHECK_0002'],
        ];

        yield 'foo_integer = 42 && foo_boolean = true' => [
            new LogicalAnd([
                new IntegerAttribute('foo_integer', 42),
                new CheckboxAttribute('foo_boolean', true),
            ]),
            ['ATTRIBUTE_SEARCH_CHECK_0003'],
        ];

        yield 'foo_integer = 42 || foo_integer = 96' => [
            new LogicalOr([
                new IntegerAttribute('foo_integer', 42),
                new IntegerAttribute('foo_integer', 96),
            ]),
            ['ATTRIBUTE_SEARCH_CHECK_0001', 'ATTRIBUTE_SEARCH_CHECK_0003', 'TROUSERS_0001'],
        ];

        yield 'foo_integer = 42 || foo_boolean = null' => [
            new LogicalOr([
                new IntegerAttribute('foo_integer', 42),
                new CheckboxAttribute('foo_boolean', null),
            ]),
            ['ATTRIBUTE_SEARCH_CHECK_0002', 'ATTRIBUTE_SEARCH_CHECK_0003', 'TROUSERS_0001'],
        ];

        $greaterThanOrEqual = new IntegerAttribute('foo_integer', 42);
        $greaterThanOrEqual->setOperator('>=');

        yield 'foo_integer >= 42' => [
            $greaterThanOrEqual,
            ['ATTRIBUTE_SEARCH_CHECK_0001', 'ATTRIBUTE_SEARCH_CHECK_0003', 'ATTRIBUTE_SEARCH_CHECK_0004', 'TROUSERS_0001'],
        ];

        $lowerThan = new IntegerAttribute('foo_integer', 100);
        $lowerThan->setOperator('<');

        yield 'foo_integer < 100' => [
            $lowerThan,
            ['ATTRIBUTE_SEARCH_CHECK_0001', 'ATTRIBUTE_SEARCH_CHECK_0003', 'TROUSERS_0001'],
        ];

        yield 'foo_integer >= 42 && foo_integer < 100' => [
            new LogicalAnd([
                $greaterThanOrEqual,
                $lowerThan,
            ]),
            ['ATTRIBUTE_SEARCH_CHECK_0001', 'ATTRIBUTE_SEARCH_CHECK_0003', 'TROUSERS_0001'],
        ];

        yield 'foo_selection = "ATTRIBUTE_SEARCH_CHECK_SELECTION_1"' => [
            new SelectionAttribute('foo_selection', ['ATTRIBUTE_SEARCH_CHECK_SELECTION_1']),
            ['ATTRIBUTE_SEARCH_CHECK_0003', 'ATTRIBUTE_SEARCH_CHECK_0004'],
        ];

        yield 'foo_selection = "ATTRIBUTE_SEARCH_CHECK_SELECTION_1" && foo_color = "#FFFFFF"' => [
            new LogicalAnd([
                new SelectionAttribute('foo_selection', ['ATTRIBUTE_SEARCH_CHECK_SELECTION_1']),
                new ColorAttribute('foo_color', [self::COLOR_WHITE]),
            ]),
            ['ATTRIBUTE_SEARCH_CHECK_0003'],
        ];

        yield 'foo_selection = "ATTRIBUTE_SEARCH_CHECK_SELECTION_1" && (foo_color = "#FFFFFF" || foo_color = NULL)' => [
            new LogicalAnd([
                new SelectionAttribute('foo_selection', ['ATTRIBUTE_SEARCH_CHECK_SELECTION_1']),
                new LogicalOr([
                    new ColorAttribute('foo_color', [self::COLOR_WHITE]),
                    new ColorAttribute('foo_color', null),
                ]),
            ]),
            ['ATTRIBUTE_SEARCH_CHECK_0003', 'ATTRIBUTE_SEARCH_CHECK_0004'],
        ];
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface,
     *     string[],
     * }>
     */
    public static function provideStockFilters(): iterable
    {
        yield 'Stock = NULL' => [new ProductStock(null), [
            '0001',
            '0003',
            'ATTRIBUTE_SEARCH_CHECK_0001',
            'ATTRIBUTE_SEARCH_CHECK_0002',
            'ATTRIBUTE_SEARCH_CHECK_0003',
            'ATTRIBUTE_SEARCH_CHECK_0004',
            'JEANS_A',
            'JEANS_B',
            'SHIRTA',
            'SHIRTB',
            'TROUSERS_0001',
            'WARRANTY_0001',
        ]];
        yield 'Stock = 0' => [new ProductStock(0, '='), []];
        yield 'Stock = 10' => [new ProductStock(10, '='), ['0002']];
        yield 'Stock = 11' => [new ProductStock(11, '='), []];
        yield 'Stock > 0' => [new ProductStock(0, '>'), ['0002']];
        yield 'Stock > 10' => [new ProductStock(10, '>'), []];
        yield 'Stock > 11' => [new ProductStock(11, '>'), []];
        yield 'Stock < 0' => [new ProductStock(0, '<'), []];
        yield 'Stock < 10' => [new ProductStock(10, '<'), []];
        yield 'Stock < 11' => [new ProductStock(11, '<'), ['0002']];
        yield 'Stock <= 0' => [new ProductStock(0, '<='), []];
        yield 'Stock <= 10' => [new ProductStock(10, '<='), ['0002']];
        yield 'Stock <= 11' => [new ProductStock(11, '<='), ['0002']];
        yield 'Stock >= 0' => [new ProductStock(0, '>='), ['0002']];
        yield 'Stock >= 10' => [new ProductStock(10, '>='), ['0002']];
        yield 'Stock >= 11' => [new ProductStock(11, '>='), []];
        yield 'Stock range min = 0' => [new ProductStockRange(0), ['0002']];
        yield 'Stock range min = 10' => [new ProductStockRange(10), ['0002']];
        yield 'Stock range min = 11' => [new ProductStockRange(11), []];
        yield 'Stock range max = 0' => [new ProductStockRange(null, 0), []];
        yield 'Stock range max = 10' => [new ProductStockRange(null, 10), ['0002']];
        yield 'Stock range max = 11' => [new ProductStockRange(null, 11), ['0002']];
        yield 'Stock range min = 0 and max = 0' => [new ProductStockRange(0, 0), []];
        yield 'Stock range min = 0 and max = 10' => [new ProductStockRange(0, 10), ['0002']];
        yield 'Stock range min = 0 and max = 11' => [new ProductStockRange(0, 11), ['0002']];
        yield 'Stock range min = 10 and max = 11' => [new ProductStockRange(10, 11), ['0002']];
        yield 'Stock range min = 10 and max = 10' => [new ProductStockRange(10, 10), ['0002']];
    }

    /**
     * @group solr-incomplete
     *
     * @param string[] $expectedIdentifiers
     *
     * @dataProvider provideForAttributeFiltering
     * @dataProvider provideStockFilters
     */
    public function testAttributeFiltering(CriterionInterface $criterion, array $expectedIdentifiers = []): void
    {
        $query = new ProductQuery(null, $criterion);

        self::ensureSearchIndexIsUpdated();
        $productService = self::getProductService();
        $productList = $productService->findProducts($query);

        $identifiers = [];
        foreach ($productList as $product) {
            $identifiers[] = $product->getCode();
        }

        self::assertEqualsCanonicalizing($expectedIdentifiers, $identifiers);
        self::assertContainsOnlyInstancesOf(ProductInterface::class, $productList);
    }

    public function testAddProductTranslation(): void
    {
        $product = self::getLocalProductService()->getProduct(self::PRODUCT_WITH_VARIANTS_CODE);
        self::assertInstanceOf(ContentAwareProductInterface::class, $product);

        $count = self::getProductsCount();

        $this->assertAvailableTranslations(['eng-GB', 'ger-DE'], $product);

        $productService = self::getLocalProductService();
        $updateStruct = $productService->newProductUpdateStruct($product);
        $updateStruct->setField('name', 'Translation', 'pol-PL');

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $product */
        $product = $productService->updateProduct($updateStruct);

        $this->assertAvailableTranslations(['eng-GB', 'ger-DE', 'pol-PL'], $product);
        self::assertCountProductsInDatabaseTable($count);
    }

    /**
     * @modifiesSearchIndex
     */
    public function testDeleteProductTranslation(): void
    {
        $product = self::getLocalProductService()->getProduct(self::PRODUCT_WITH_VARIANTS_CODE);
        self::assertInstanceOf(ContentAwareProductInterface::class, $product);

        $this->assertAvailableTranslations(['eng-GB', 'ger-DE'], $product);

        $languageService = self::getLanguageService();

        $productService = self::getLocalProductService();
        $productService->deleteProductTranslation($product, $languageService->loadLanguage('ger-DE'));

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $product */
        $product = $productService->getProduct($product->getCode());

        $this->assertAvailableTranslations(['eng-GB'], $product);
    }

    /**
     * @modifiesSearchIndex
     */
    public function testDeleteProduct(): void
    {
        $count = self::getProductsCount();
        self::assertGreaterThan(0, $count);

        $product = self::getLocalProductService()->getProduct('0001');
        $productService = self::getLocalProductService();
        $productService->deleteProduct($product);

        self::assertCountProductsInDatabaseTable($count - 1);
    }

    /**
     * @modifiesSearchIndex
     */
    public function testDeleteBaseProduct(): void
    {
        $count = self::getProductsCount();
        self::assertGreaterThan(0, $count);

        $productService = self::getLocalProductService();
        $product = $productService->getProduct(self::PRODUCT_WITH_VARIANTS_CODE);
        $productService->deleteProduct($product);

        self::assertCountProductsInDatabaseTable($count - 1);
    }

    /**
     * @modifiesSearchIndex
     */
    public function testDeleteBaseProductContainingVariants(): void
    {
        $count = self::getProductsCount();
        self::assertGreaterThan(0, $count);

        $productService = self::getLocalProductService();
        $product = $productService->getProduct(self::PRODUCT_WITH_VARIANTS_CODE);

        $productVariantCode = 'TROUSERS_0001V4';
        $productService->createProductVariants(
            $product,
            [
                new ProductVariantCreateStruct(
                    [
                        'bar' => true,
                        'baz' => 10,
                    ],
                    $productVariantCode
                ),
            ]
        );

        $productService->deleteProduct($product);
        self::assertCountProductsInDatabaseTable($count - 1);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\ProductCatalog\Local\Persistence\Values\Product' with identifier '$productVariantCode'");

        $productService->getProduct($productVariantCode);
    }

    public function testDeleteProductVariantsByBaseProduct(): void
    {
        $productService = self::getLocalProductService();
        $product = $productService->getProduct(self::PRODUCT_WITH_VARIANTS_CODE);

        $productService->createProductVariants(
            $product,
            [
                new ProductVariantCreateStruct(
                    [
                        'bar' => true,
                        'baz' => 10,
                    ],
                    'TROUSERS_0001V1'
                ),
                new ProductVariantCreateStruct(
                    [
                        'bar' => true,
                        'baz' => 100,
                    ],
                    'TROUSERS_0001V2'
                ),
                new ProductVariantCreateStruct(
                    [
                        'bar' => true,
                        'baz' => 1000,
                    ],
                    'TROUSERS_0001V3'
                ),
            ]
        );

        $variants = $productService->findProductVariants($product)->getVariants();
        self::assertCount(3, $variants);

        $variantCodes = $productService->deleteProductVariantsByBaseProduct($product);
        self::assertSame(['TROUSERS_0001V1', 'TROUSERS_0001V2', 'TROUSERS_0001V3'], $variantCodes);

        $variants = $productService->findProductVariants($product)->getVariants();
        self::assertCount(0, $variants);
    }

    /**
     * @return iterable<string, array{string,string}>
     */
    public function dataProviderForInvalidProductCode(): iterable
    {
        yield 'empty code' => [
            '',
            'Product code must be set',
        ];

        yield 'invalid characters' => [
            '!@#$%^&*',
            'Product code may only contain letters from "a" to "z", numbers, underscores and dashes',
        ];

        yield 'too long code' => [
            str_repeat('X', 128),
            'Product code is too long. It should have 64 character or less',
        ];

        yield 'non-unique' => [
            '0001',
            "Product with code '0001' already exists",
        ];
    }

    /**
     * @param string[] $expectedLanguageCodes
     */
    private function assertAvailableTranslations(
        array $expectedLanguageCodes,
        ContentAwareProductInterface $product
    ): void {
        self::assertEqualsCanonicalizing(
            $expectedLanguageCodes,
            $product->getContent()->versionInfo->languageCodes,
        );
    }

    /**
     * @param array<string,mixed> $expectedAttributes
     */
    private function assertProductVariant(
        ProductInterface $product,
        string $expectedProductCode,
        string $expectedBaseProductCode,
        array $expectedAttributes
    ): void {
        self::assertInstanceOf(ProductVariantInterface::class, $product);
        self::assertEquals($expectedProductCode, $product->getCode());
        self::assertEquals($expectedBaseProductCode, $product->getBaseProduct()->getCode());
        self::assertAttributesValue($expectedAttributes, $product);
    }

    /**
     * Note: Replace with a migration once available.
     *
     * @param mixed $attributeValue
     */
    private function setAttributeInProduct(string $productCode, string $attributeIdentifier, $attributeValue): void
    {
        $localProductService = self::getLocalProductService();

        $product = $localProductService->getProduct($productCode);
        $updateStruct = $localProductService->newProductUpdateStruct($product);
        $updateStruct->setAttribute($attributeIdentifier, $attributeValue);
        $localProductService->updateProduct($updateStruct);
    }

    private static function assertTermAggregationKeyCount(
        int $expectedCount,
        TermAggregationResultEntry $entry
    ): void {
        self::assertSame(
            $expectedCount,
            $entry->getCount(),
            sprintf('Unexpected count for key: %s', $entry->getKey()),
        );
    }

    private static function assertRangeAggregationCount(
        int $expectedCount,
        RangeAggregationResultEntry $entry
    ): void {
        $key = $entry->getKey();

        self::assertSame(
            $expectedCount,
            $entry->getCount(),
            sprintf(
                'Aggregation "%d < X < %d" expected %d results',
                $key->getFrom(),
                $key->getTo(),
                $expectedCount,
            )
        );
    }

    private static function assertBooleanAggregation(
        AggregationResult $aggregation,
        string $aggregationName,
        int $trueCount,
        int $falseCount
    ): void {
        self::assertInstanceOf(TermAggregationResult::class, $aggregation);
        self::assertSame($aggregationName, $aggregation->getName());
        $entries = $aggregation->getEntries();
        if ($entries instanceof Traversable) {
            $entries = iterator_to_array($entries);
        }

        self::assertCount(2, $entries);
        foreach ($entries as $entry) {
            self::assertInstanceOf(TermAggregationResultEntry::class, $entry);
            self::assertIsBool($entry->getKey());
            if ($entry->getKey() === true) {
                self::assertTermAggregationKeyCount($trueCount, $entry);
            } else {
                self::assertTermAggregationKeyCount($falseCount, $entry);
            }
        }
    }

    private static function assertStatsAggregation(
        AggregationResult $aggregation,
        string $aggregationName,
        float $sum,
        int $count,
        float $min,
        float $max,
        float $avg,
        int $delta = 2
    ): void {
        self::assertInstanceOf(StatsAggregationResult::class, $aggregation);
        self::assertSame($aggregationName, $aggregation->getName());

        self::assertSame($sum, $aggregation->getSum());
        self::assertSame($count, $aggregation->getCount());
        self::assertEqualsWithDelta($min, $aggregation->getMin(), $delta);
        self::assertEqualsWithDelta($max, $aggregation->getMax(), $delta);
        self::assertEqualsWithDelta($avg, $aggregation->getAvg(), $delta);
    }

    private static function assertPriceStatsAggregation(
        AggregationResult $aggregation,
        string $aggregationName,
        Money $sum,
        int $count,
        Money $min,
        Money $max,
        Money $avg
    ): void {
        self::assertInstanceOf(PriceStatsAggregationResult::class, $aggregation);
        self::assertEquals($aggregationName, $aggregation->getName());

        self::assertNotNull($aggregation->getSum());
        self::assertTrue($sum->equals($aggregation->getSum()));
        self::assertEquals($count, $aggregation->getCount());
        self::assertNotNull($aggregation->getMin());
        self::assertTrue($min->equals($aggregation->getMin()));
        self::assertNotNull($aggregation->getMax());
        self::assertTrue($max->equals($aggregation->getMax()));
        self::assertNotNull($aggregation->getAvg());
        self::assertTrue($avg->equals($aggregation->getAvg()));
    }

    /**
     * @param array<string, positive-int> $aggregationResults
     */
    private static function assertSelectionAggregation(
        AggregationResult $aggregation,
        string $aggregationName,
        array $aggregationResults
    ): void {
        self::assertInstanceOf(TermAggregationResult::class, $aggregation);
        self::assertSame($aggregationName, $aggregation->getName());

        foreach ($aggregation->getEntries() as $entry) {
            $key = $entry->getKey();

            self::assertArrayHasKey($key, $aggregationResults, 'Unrecognized aggregation entry');
            self::assertTermAggregationKeyCount($aggregationResults[$key], $entry);
        }
    }

    /**
     * @param array<string, int<0, max>> $aggregationResults
     * @param (callable(mixed):string)|null $keyFormatter
     */
    private static function assertRangeAggregation(
        AggregationResult $aggregation,
        string $aggregationName,
        array $aggregationResults,
        callable $keyFormatter = null
    ): void {
        $keyFormatter ??= static fn ($value): string => (string)$value;
        self::assertInstanceOf(RangeAggregationResult::class, $aggregation);
        self::assertSame($aggregationName, $aggregation->getName());

        $entries = $aggregation->getEntries();
        if ($entries instanceof Traversable) {
            $entries = iterator_to_array($entries);
        }

        self::assertCount(count($aggregationResults), $entries);
        foreach ($entries as $entry) {
            self::assertInstanceOf(RangeAggregationResultEntry::class, $entry);
            $key = $entry->getKey();

            $identifier = sprintf(
                '%s:%s',
                $key->getFrom() === null ? '~' : $keyFormatter($key->getFrom()),
                $key->getTo() === null ? '~' : $keyFormatter($key->getTo()),
            );

            self::assertArrayHasKey($identifier, $aggregationResults, 'Unrecognized aggregation entry');
            self::assertRangeAggregationCount($aggregationResults[$identifier], $entry);
        }
    }
}
