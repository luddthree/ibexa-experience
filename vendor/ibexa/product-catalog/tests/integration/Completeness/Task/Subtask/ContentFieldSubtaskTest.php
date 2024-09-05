<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Completeness\Task\Subtask;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\Subtask\ContentFieldSubtask;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Tests\Integration\ProductCatalog\Completeness\Task\BaseTaskTest;

final class ContentFieldSubtaskTest extends BaseTaskTest
{
    /**
     * @dataProvider provideForTestGetEntry
     */
    public function testGetEntry(
        string $productCode,
        float $completenessPercentage,
        bool $isComplete,
        string $fieldIdentifier,
        ?string $languageCode
    ): void {
        $product = self::getProductService()->getProduct($productCode);

        if (!$product instanceof ContentAwareProductInterface) {
            return;
        }

        $contentFieldSubtask = $this->getContentFieldSubtask(
            $product->getContent(),
            $fieldIdentifier,
            $languageCode
        );

        if ($contentFieldSubtask === null) {
            return;
        }

        $entry = $contentFieldSubtask->getEntry($product);
        $this->assertCompleteness($entry, $isComplete, $completenessPercentage);
    }

    /**
     * @phpstan-return iterable<array{string, float, boolean, string|null}>
     */
    public function provideForTestGetEntry(): iterable
    {
        yield [
            'JEANS_1',
            100,
            true,
            'name',
            'pol-PL',
        ];

        yield [
            'BLOUSE_1',
            0,
            false,
            'name',
            null,
        ];
    }

    /**
     * @dataProvider provideForTestGetSubtaskGroups
     *
     * @phpstan-param mixed $expectedTasks
     */
    public function testGetSubtaskGroups(
        string $productCode,
        $expectedTasks,
        string $languageCode
    ): void {
        $product = self::getProductService()->getProduct($productCode);

        if (!$product instanceof ContentAwareProductInterface) {
            return;
        }

        $contentFieldSubtask = $this->getContentFieldSubtask(
            $product->getContent(),
            'name',
            $languageCode
        );

        if ($contentFieldSubtask === null) {
            return;
        }

        self::assertEquals(
            $expectedTasks,
            $contentFieldSubtask->getSubtaskGroups($product)
        );
    }

    /**
     * @phpstan-return iterable<array{string,null,string}>
     */
    public function provideForTestGetSubtaskGroups(): iterable
    {
        yield ['JEANS_1', null, 'eng-GB'];
    }

    private function getContentFieldSubtask(
        Content $content,
        string $fieldIdentifier,
        ?string $languageCode
    ): ?TaskInterface {
        $field = $content->getField($fieldIdentifier);
        if ($field === null) {
            return null;
        }

        return new ContentFieldSubtask(
            $content,
            $field,
            self::getFieldHelper(),
            $languageCode
        );
    }
}
