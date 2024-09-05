<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

abstract class AbstractCriterionTestCase extends IbexaKernelTestCase
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
     * @dataProvider dataProviderForTestCriterion
     *
     * @param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface|callable(): CriterionInterface $criterion
     * @param string[] $expectedProductSearchResults
     */
    final public function testCriterion($criterion, array $expectedProductSearchResults): void
    {
        self::setAdministratorUser();

        if (is_callable($criterion)) {
            // Initialize "lazy" criterion
            $criterion = $criterion();
        }

        if ($this->isSearchEngineNotSupported()) {
            self::markTestSkipped(sprintf(
                'Criterion %s is not supported in current search engine',
                get_class($criterion)
            ));
        }

        $actualProductSearchResults = self::getProductService()->findProducts(
            new ProductQuery($criterion)
        );

        $this->assertProductSearchResults($expectedProductSearchResults, $actualProductSearchResults);
    }

    /**
     * @phpstan-return iterable<array{CriterionInterface|callable(): CriterionInterface, string[]}>
     */
    abstract public function dataProviderForTestCriterion(): iterable;

    /**
     * @param string[] $expectedProductSearchResults
     * @param \Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface $actualProductSearchResults
     */
    protected function assertProductSearchResults(
        array $expectedProductSearchResults,
        ProductListInterface $actualProductSearchResults
    ): void {
        $actualProductsCodes = [];
        foreach ($actualProductSearchResults as $product) {
            $actualProductsCodes[] = $product->getCode();
        }

        self::assertEqualsCanonicalizing($expectedProductSearchResults, $actualProductsCodes);
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
