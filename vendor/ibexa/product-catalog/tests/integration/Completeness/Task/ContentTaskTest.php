<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Completeness\Task;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\ContentTask;
use Ibexa\Bundle\ProductCatalog\UI\Language\PreviewLanguageResolverInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ContentTaskTest extends BaseTaskTest
{
    private ContentTask $contentTask;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contentTask = new ContentTask(
            self::getFieldHelper(),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(PreviewLanguageResolverInterface::class)
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
        $entry = $this->contentTask->getEntry($product);

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
            0,
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
        $this->assertSubtasks($this->contentTask, $productCode, $expectedCount, $expectations);
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
                self::assertCount(1, $taskGroups);

                $taskGroup = $taskGroups[0];
                self::assertSame('default', $taskGroup->getIdentifier());

                foreach ($taskGroup->getTasks() as $task) {
                    self::assertSame('Name', $task->getName());
                }
            },
        ];

        yield [
            'BLOUSE_1',
            1,
            static function (array $taskGroups): void {
                self::assertCount(1, $taskGroups);

                $taskGroup = $taskGroups[0];
                self::assertSame('default', $taskGroup->getIdentifier());

                foreach ($taskGroup->getTasks() as $task) {
                    self::assertSame('Name', $task->getName());
                }
            },
        ];
    }
}
