<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Completeness\Task;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\AttributesTask;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AttributesTaskTest extends BaseTaskTest
{
    private AttributesTask $attributesTask;

    protected function setUp(): void
    {
        parent::setUp();

        $this->attributesTask = new AttributesTask(
            $this->createMock(TranslatorInterface::class)
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
        $entry = $this->attributesTask->getEntry($product);

        $this->assertCompleteness($entry, $isComplete, $completenessPercentage);
    }

    /**
     * @phpstan-return iterable<array{string, float, boolean}>
     */
    public function provideForTestGetEntry(): iterable
    {
        yield [
            'JEANS_1',
            100,
            true,
        ];

        yield [
            'BLOUSE_1',
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
        $this->assertSubtasks($this->attributesTask, $productCode, $expectedCount, $expectations);
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
            1,
            static function (array $taskGroups): void {
                self::assertSame([], $taskGroups);
            },
        ];

        yield [
            'BLOUSE_1',
            1,
            static function (array $taskGroups): void {
                [$taskGroup1, $taskGroup2] = $taskGroups;

                self::assertSame('dimensions', $taskGroup1->getIdentifier());
                self::assertSame('dress_size', $taskGroup2->getIdentifier());

                $task1 = $taskGroup1->getTasks()[0];
                $task2 = $taskGroup2->getTasks()[0];

                self::assertSame('bar_attribute_task', $task1->getIdentifier());
                self::assertSame('foobar_attribute_task', $task2->getIdentifier());
            },
        ];
    }
}
