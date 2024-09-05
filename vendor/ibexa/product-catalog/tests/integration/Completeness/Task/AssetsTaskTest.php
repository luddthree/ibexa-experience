<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Completeness\Task;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\AssetsTask;
use Ibexa\Contracts\ProductCatalog\AssetServiceInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AssetsTaskTest extends BaseTaskTest
{
    private AssetsTask $assetsTask;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assetsTask = new AssetsTask(
            $this->createMock(AssetServiceInterface::class),
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
        $entry = $this->assetsTask->getEntry($product);

        $this->assertCompleteness($entry, $isComplete, $completenessPercentage);
    }

    /**
     * @phpstan-return iterable<array{string, float, boolean}>
     */
    public function provideForTestGetEntry(): iterable
    {
        yield [
            'BLOUSE_1',
            0.0,
            false,
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

        self::assertEquals(
            $expectedTasks,
            $this->assetsTask->getSubtaskGroups($product)
        );
    }

    /**
     * @phpstan-return iterable<array{string,null}>
     */
    public function provideForTestGetSubtaskGroups(): iterable
    {
        yield ['JEANS_1', null];
    }
}
