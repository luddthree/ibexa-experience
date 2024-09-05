<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Completeness\Task;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\PricesTask;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PricesTaskTest extends BaseTaskTest
{
    private const CURRENCY_1 = 'EUR';
    private const CURRENCY_2 = 'USD';
    private const CURRENCY_3 = 'BTC';

    private PricesTask $pricesTask;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pricesTask = new PricesTask(
            self::getProductPriceService(),
            self::getCurrencyService(),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(RouterInterface::class)
        );
    }

    /**
     * @dataProvider provideForTestGetEntry
     */
    public function testGetEntry(
        string $productCode,
        float $completenessPercentage,
        bool $isComplete
    ): void {
        $product = self::getProductService()->getProduct($productCode);
        $entry = $this->pricesTask->getEntry($product);

        $this->assertCompleteness($entry, $isComplete, $completenessPercentage);
    }

    /**
     * @phpstan-return iterable<array{string, float, boolean}>
     */
    public function provideForTestGetEntry(): iterable
    {
        yield [
            '0001',
            0,
            false,
        ];

        yield [
            'BLOUSE_1',
            33.33333333333333,
            false,
        ];

        yield [
            'JEANS_1',
            100,
            true,
        ];
    }

    /**
     * @dataProvider provideForTestGetSubtaskGroups
     *
     * @phpstan-param callable(array<\Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskGroup> $taskGroups): void $expectations
     */
    public function testGetSubtaskGroups(string $productCode, int $expectedCount, callable $expectations): void
    {
        $this->assertSubtasks($this->pricesTask, $productCode, $expectedCount, $expectations);
    }

    /**
     * @phpstan-return iterable<array{
     *     string,
     *     int,
     *     callable(array<\Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskGroup> $taskGroups): void,
     * }>
     */
    public function provideForTestGetSubtaskGroups(): iterable
    {
        yield [
            'JEANS_1',
            3,
            static function (array $taskGroups): void {
                self::assertCount(1, $taskGroups);

                $taskGroup = $taskGroups[0];
                self::assertSame('default', $taskGroup->getIdentifier());

                [$task1, $task2, $task3] = $taskGroup->getTasks();

                self::assertSame(self::CURRENCY_1, $task1->getName());
                self::assertSame(self::CURRENCY_2, $task2->getName());
                self::assertSame(self::CURRENCY_3, $task3->getName());
            },
        ];

        yield [
            'BLOUSE_1',
            3,
            static function (array $taskGroups): void {
                self::assertCount(1, $taskGroups);

                $taskGroup = $taskGroups[0];
                self::assertSame('default', $taskGroup->getIdentifier());

                [$task1, $task2, $task3] = $taskGroup->getTasks();

                self::assertSame(self::CURRENCY_1, $task1->getName());
                self::assertSame(self::CURRENCY_2, $task2->getName());
                self::assertSame(self::CURRENCY_3, $task3->getName());
            },
        ];
    }
}
