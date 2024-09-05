<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Completeness\Task;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TranslationsTask;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TranslationsTaskTest extends BaseTaskTest
{
    private const TRANSLATION_1 = 'English (American)';
    private const TRANSLATION_2 = 'German';
    private const TRANSLATION_3 = 'English (United Kingdom)';
    private const TRANSLATION_4 = 'Polish';
    private const TRANSLATION_5 = 'French';

    private TranslationsTask $translationsTask;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translationsTask = new TranslationsTask(
            self::getLanguageService(),
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
        $entry = $this->translationsTask->getEntry($product);

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
            20.0,
            false,
        ];
    }

    /**
     * @dataProvider provideForTestGetSubtaskGroups
     *
     * @phpstan-param callable(array<\Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskGroup> $taskGroups): void $expectations
     */
    public function testGetSubtaskGroups(string $productCode, int $expectedCount, callable $expectations): void
    {
        $this->assertSubtasks($this->translationsTask, $productCode, $expectedCount, $expectations);
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
            5,
            static function (array $taskGroups): void {
                self::assertCount(1, $taskGroups);

                $taskGroup = $taskGroups[0];
                self::assertSame('default', $taskGroup->getIdentifier());

                [$task1, $task2, $task3, $task4, $task5] = $taskGroup->getTasks();

                self::assertSame(self::TRANSLATION_1, $task1->getName());
                self::assertSame(self::TRANSLATION_2, $task2->getName());
                self::assertSame(self::TRANSLATION_3, $task3->getName());
                self::assertSame(self::TRANSLATION_4, $task4->getName());
                self::assertSame(self::TRANSLATION_5, $task5->getName());
            },
        ];

        yield [
            'BLOUSE_1',
            5,
            static function (array $taskGroups): void {
                self::assertCount(1, $taskGroups);

                $taskGroup = $taskGroups[0];
                self::assertSame('default', $taskGroup->getIdentifier());

                [$task1, $task2, $task3, $task4, $task5] = $taskGroup->getTasks();

                self::assertSame(self::TRANSLATION_1, $task1->getName());
                self::assertSame(self::TRANSLATION_2, $task2->getName());
                self::assertSame(self::TRANSLATION_3, $task3->getName());
                self::assertSame(self::TRANSLATION_4, $task4->getName());
                self::assertSame(self::TRANSLATION_5, $task5->getName());
            },
        ];
    }
}
