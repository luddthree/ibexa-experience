<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Completeness\Task\Subtask;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\Subtask\PriceCurrencySubtask;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Tests\Integration\ProductCatalog\Completeness\Task\BaseTaskTest;
use Symfony\Component\Routing\RouterInterface;

final class PriceCurrencySubtaskTest extends BaseTaskTest
{
    /**
     * @dataProvider provideForTestGetEntry
     */
    public function testGetEntry(
        string $productCode,
        float $completenessPercentage,
        bool $isComplete,
        string $currencyCode
    ): void {
        $product = self::getProductService()->getProduct($productCode);
        $priceCurrencySubtask = $this->getPriceCurrencySubtask($product, $currencyCode);

        $entry = $priceCurrencySubtask->getEntry($product);
        $this->assertCompleteness($entry, $isComplete, $completenessPercentage);
    }

    /**
     * @phpstan-return iterable<array{string, float, boolean, string}>
     */
    public function provideForTestGetEntry(): iterable
    {
        yield [
            'JEANS_1',
            100,
            true,
            'EUR',
        ];

        yield [
            'JEANS_1',
            0,
            false,
            'ABC',
        ];

        yield [
            'BLOUSE_1',
            100,
            true,
            'EUR',
        ];

        yield [
            'BLOUSE_1',
            0,
            false,
            'DEF',
        ];
    }

    /**
     * @dataProvider provideForTestGetSubtaskGroups
     *
     * @phpstan-param mixed $expectedTasks
     */
    public function testGetSubtaskGroups(string $productCode, $expectedTasks): void
    {
        $product = self::getProductService()->getProduct($productCode);
        $priceCurrencySubtask = $this->getPriceCurrencySubtask($product, 'EUR');

        self::assertEquals(
            $expectedTasks,
            $priceCurrencySubtask->getSubtaskGroups($product)
        );
    }

    /**
     * @phpstan-return iterable<array{string,null}>
     */
    public function provideForTestGetSubtaskGroups(): iterable
    {
        yield ['JEANS_1', null];
    }

    private function getPriceCurrencySubtask(
        ProductInterface $product,
        string $currencyCode
    ): TaskInterface {
        $currency = $this->getCurrencyMock($currencyCode);
        $definedPrices = self::getProductPriceService()
            ->findPricesByProductCode($product->getCode())
            ->getPrices();

        return new PriceCurrencySubtask(
            $currency,
            $this->createMock(RouterInterface::class),
            $definedPrices
        );
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getCurrencyMock(string $currencyCode): CurrencyInterface
    {
        $currencyMock = $this->createMock(CurrencyInterface::class);
        $currencyMock->method('getCode')->willReturn($currencyCode);

        return $currencyMock;
    }
}
