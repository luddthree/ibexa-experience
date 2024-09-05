<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Bridge;

use Ibexa\Bundle\Commerce\Eshop\Catalog\CatalogNode;
use Ibexa\Bundle\Commerce\Eshop\Content\Fields\PriceField;
use Ibexa\Bundle\Commerce\Eshop\Content\Fields\StockField;
use Ibexa\Bundle\Commerce\Eshop\Content\ValueObject;
use Ibexa\Bundle\Commerce\Eshop\Product\ProductNode;
use Ibexa\Bundle\Commerce\Eshop\Services\Url\BaseCatalogUrl;
use Ibexa\Bundle\Commerce\Price\Model\Price;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Migration\Repository\Migration;
use Ibexa\ProductCatalog\Bridge\CatalogDataProvider;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

final class CatalogDataProviderTest extends IbexaKernelTestCase
{
    private const TOTAL_COUNT = 16;

    protected function setUp(): void
    {
        self::bootKernel();
        self::getLanguageResolver()->setContextLanguage('eng-US');
        self::setAdministratorUser();

        $this->replaceSyntheticServicesWithMocks();
        $this->executeCatalogDataProviderTestMigration('legacy_commerce_bridge_setup.yaml');
    }

    protected function tearDown(): void
    {
        $this->executeCatalogDataProviderTestMigration('legacy_commerce_bridge_teardown.yaml');
    }

    public function testFetchChildrenList(): void
    {
        $childrenList = $this->getCatalogDataProvider()->fetchChildrenList(
            'all',
            0,
            ['filterType' => 'productList'],
        );

        self::assertEquals(self::TOTAL_COUNT, $childrenList->catalogElementCount);
        self::assertEquals(3, $childrenList->countChildren());
        self::assertValueObjectList(
            [
                [
                    'name' => 'Dress A',
                    'identifier' => '0001',
                    'url' => '/product?contentId=66',
                ],
                [
                    'name' => 'Dress B',
                    'identifier' => '0002',
                    'url' => '/product?contentId=67',
                ],
                [
                    'name' => 'Dress C',
                    'identifier' => '0003',
                    'url' => '/product?contentId=68',
                ],
            ],
            CatalogNode::class,
            $childrenList->elements
        );
    }

    public function testCountChildrenList(): void
    {
        $count = $this->getCatalogDataProvider()->countChildrenList(
            'all',
            0,
            ['filterType' => 'productList']
        );

        self::assertEquals(self::TOTAL_COUNT, $count);
    }

    public function testFetchElementByIdentifier(): void
    {
        $element = $this->getCatalogDataProvider()->fetchElementByIdentifier('0002');

        self::assertInstanceOf(ProductNode::class, $element);
        self::assertProperties([
            'identifier' => '0002',
            'sku' => '0002',
            'name' => 'Dress B',
            'url' => '/product?contentId=67',
            'stock' => new StockField([
                'stockNumeric' => 10,
            ]),
        ], $element);
    }

    /**
     * @param mixed $identifier
     *
     * @dataProvider dataProviderForTestFetchElementByIdentifierThrowsInvalidArgumentException
     */
    public function testFetchElementByIdentifierThrowsInvalidArgumentException($identifier, string $expectedMessage): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage($expectedMessage);

        $this->getCatalogDataProvider()->fetchElementByIdentifier($identifier);
    }

    /**
     * @return iterable<string, array{mixed,string}>
     */
    public function dataProviderForTestFetchElementByIdentifierThrowsInvalidArgumentException(): iterable
    {
        yield 'null' => [
            null,
            "Argument '\$identifier' is invalid: 'NULL' is incorrect value",
        ];

        yield 'int' => [
            7623,
            "Argument '\$identifier' is invalid: '7623' is incorrect value",
        ];
    }

    public function testFetchElementBySku(): void
    {
        $element = $this->getCatalogDataProvider()->fetchElementByIdentifier('0002');

        self::assertInstanceOf(ProductNode::class, $element);
        self::assertProperties([
            'identifier' => '0002',
            'sku' => '0002',
            'name' => 'Dress B',
            'url' => '/product?contentId=67',
            'stock' => new StockField([
                'stockNumeric' => 10,
            ]),
        ], $element);
    }

    /**
     * @dataProvider dataProviderForCreateCatalogNode
     *
     * @param array<string,mixed> $expectedProperties
     */
    public function testCreateCatalogNode(string $code, array $expectedProperties): void
    {
        $element = $this->getCatalogDataProvider()->fetchElementBySku($code);

        self::assertInstanceOf(ProductNode::class, $element);
        self::assertProperties($expectedProperties, $element);
    }

    /**
     * @return iterable<string,array{string,array<string,mixed>}>
     */
    public function dataProviderForCreateCatalogNode(): iterable
    {
        yield 'typical' => [
            '0002',
            [
                'identifier' => '0002',
                'sku' => '0002',
                'name' => 'Dress B',
                'stock' => new StockField([
                    'stockNumeric' => 10,
                ]),
                'price' => new PriceField([
                    'price' => new Price([
                        'price' => 112.0,
                        'priceInclVat' => 112.0,
                        'priceExclVat' => 100.0,
                        'isVatPrice' => true,
                        'vatPercent' => 12.0,
                        'currency' => 'EUR',
                        'source' => 'Ibexa',
                    ]),
                ]),
            ],
        ];

        yield 'not available' => [
            'NOT_AVAILABLE',
            [
                'identifier' => 'NOT_AVAILABLE',
                'sku' => 'NOT_AVAILABLE',
                'name' => 'NOT_AVAILABLE',
                'stock' => null,
            ],
        ];

        yield 'limited stock' => [
            'LIMITED_STOCK',
            [
                'identifier' => 'LIMITED_STOCK',
                'sku' => 'LIMITED_STOCK',
                'name' => 'LIMITED_STOCK',
                'stock' => new StockField([
                    'stockNumeric' => 10,
                ]),
            ],
        ];

        yield 'infinite stock' => [
            'INFINITE_STOCK',
            [
                'identifier' => 'INFINITE_STOCK',
                'sku' => 'INFINITE_STOCK',
                'name' => 'INFINITE_STOCK',
                'stock' => new StockField([
                    'stockNumeric' => PHP_INT_MAX,
                ]),
            ],
        ];
    }

    private function replaceSyntheticServicesWithMocks(): void
    {
        self::getContainer()->set(BaseCatalogUrl::class, $this->createMock(BaseCatalogUrl::class));
    }

    private function executeCatalogDataProviderTestMigration(string $name): void
    {
        $content = file_get_contents(__DIR__ . '/../_migrations/' . $name);
        if ($content === false) {
            self::fail('Unable to load test fixtures');
        }

        $migrationService = self::getContainer()->get(MigrationService::class);
        $migrationService->executeOne(new Migration(uniqid(), $content));

        self::ensureSearchIndexIsUpdated();
    }

    /**
     * @param array<string,mixed> $expectedProperties
     */
    private static function assertProperties(array $expectedProperties, ValueObject $valueObject): void
    {
        foreach ($expectedProperties as $name => $expectedValue) {
            $actualValue = $valueObject->{$name};
            if ($expectedValue instanceof PriceField && $actualValue instanceof PriceField) {
                // Workaround for time sensitive test
                self::assertEquals($expectedValue->toHash(), $actualValue->toHash());
                continue;
            }

            self::assertEquals($expectedValue, $actualValue);
        }
    }

    /**
     * @param array<array-key,array<string,mixed>> $expectedProperties
     * @param class-string $expectedClass
     * @param array<array-key,\Ibexa\Bundle\Commerce\Eshop\Content\ValueObject> $valueObjects
     */
    private static function assertValueObjectList(
        array $expectedProperties,
        string $expectedClass,
        array $valueObjects
    ): void {
        foreach ($valueObjects as $key => $valueObject) {
            self::assertInstanceOf($expectedClass, $valueObject);
            self::assertProperties($expectedProperties[$key], $valueObject);
        }
    }

    private static function getCatalogDataProvider(): CatalogDataProvider
    {
        return self::getServiceByClassName(CatalogDataProvider::class);
    }
}
