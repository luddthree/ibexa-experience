<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\SortClause;

use Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

abstract class AbstractSortClauseTestCase extends IbexaKernelTestCase
{
    final protected function setUp(): void
    {
        self::bootKernel();

        foreach ($this->getAdditionalFixtures() as $name) {
            $this->executeMigration($name . '_setup.yaml');
        }

        self::ensureSearchIndexIsUpdated();
    }

    final protected function tearDown(): void
    {
        foreach ($this->getAdditionalFixtures() as $name) {
            $this->executeMigration($name . '_teardown.yaml');
        }

        self::ensureSearchIndexIsUpdated();

        parent::tearDown();
    }

    /**
     * @dataProvider dataProviderForTestSortClause
     *
     * @param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause|callable(): SortClause $sortClause
     * @param string[] $expectedProductsOrder
     */
    final public function testSortClause(
        $sortClause,
        array $expectedProductsOrder,
        ?SortClause $additionalSortClause = null,
        ?CriterionInterface $additionalFilter = null
    ): void {
        self::setAdministratorUser();

        if (is_callable($sortClause)) {
            // Initialize "lazy" sort clause
            $sortClause = $sortClause();
        }

        if ($this->isSearchEngineNotSupported()) {
            self::markTestSkipped(sprintf(
                'Sort Clause %s is not supported in current search engine',
                get_class($sortClause)
            ));
        }

        $productService = self::getProductService();
        $sortClauses = [$sortClause];
        if ($additionalSortClause) {
            $sortClauses[] = $additionalSortClause;
        }

        $actualResults = $productService->findProducts(
            new ProductQuery($additionalFilter, null, $sortClauses)
        );

        $this->assertProductsOrder($expectedProductsOrder, $actualResults);
    }

    /**
     * @phpstan-return iterable<
     *      array{
     *          SortClause|callable(): SortClause,
     *          string[],
     *          2?: ?SortClause,
     *          3?: ?CriterionInterface,
     *      }
     * >
     */
    abstract public function dataProviderForTestSortClause(): iterable;

    /**
     * @param string[] $exceptedProductsOrder
     * @param \Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface $actualProductList
     */
    protected function assertProductsOrder(
        array $exceptedProductsOrder,
        ProductListInterface $actualProductList
    ): void {
        $actualProductsOrder = [];
        foreach ($actualProductList as $product) {
            $actualProductsOrder[] = $product->getCode();
        }

        self::assertEquals($exceptedProductsOrder, $actualProductsOrder);
    }

    /**
     * Provides additional fixtures available for criterion test.
     *
     * Fixtures should be placed under tests/integration/_migrations/ directory. Two migration files are expected:
     *
     * - `<name>_setup.yaml`: creates necessary data
     * - `<name>_teardown.yaml` clean up data created in <name>_setup.yaml
     *
     * @return string[] Fixtures names WITHOUT `_setup.yaml` or `_teardown.yaml` suffix
     */
    protected function getAdditionalFixtures(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    protected function getNotSupportedSearchEngines(): array
    {
        return [];
    }

    private function isSearchEngineNotSupported(): bool
    {
        return in_array(
            getenv('SEARCH_ENGINE') ?: 'legacy',
            $this->getNotSupportedSearchEngines(),
            true
        );
    }
}
