<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Completeness\Task\Subtask;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\Subtask\AttributeSubtask;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Tests\Integration\ProductCatalog\Completeness\Task\BaseTaskTest;

final class AttributeSubtaskTest extends BaseTaskTest
{
    /**
     * @dataProvider provideForTestGetEntry
     */
    public function testGetEntry(
        string $productCode,
        float $completenessPercentage,
        bool $isComplete,
        ?string $attributeValue
    ): void {
        $product = self::getProductService()->getProduct($productCode);
        $attributeSubtask = $this->getAttributeSubtask($attributeValue);

        $entry = $attributeSubtask->getEntry($product);
        $this->assertCompleteness($entry, $isComplete, $completenessPercentage);
    }

    /**
     * @phpstan-return iterable<array{string, float, boolean, ?string}>
     */
    public function provideForTestGetEntry(): iterable
    {
        yield [
            'BLOUSE_1',
            100,
            true,
            'test',
        ];

        yield [
            'BLOUSE_1',
            0,
            false,
            null,
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
        $attributeSubtask = $this->getAttributeSubtask('foo');

        self::assertEquals(
            $expectedTasks,
            $attributeSubtask->getSubtaskGroups($product)
        );
    }

    /**
     * @phpstan-return iterable<array{string,null}>
     */
    public function provideForTestGetSubtaskGroups(): iterable
    {
        yield ['JEANS_1', null];
    }

    private function getAttributeSubtask(?string $attributeValue): TaskInterface
    {
        $attributeMock = $this->createMock(AttributeInterface::class);
        $attributeMock->method('getValue')->willReturn($attributeValue);

        return new AttributeSubtask($attributeMock);
    }
}
