<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Iterator\BatchIteratorAdapter;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\AttributeDefinitionFetchAdapter;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\NameCriterion;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use PHPUnit\Framework\TestCase;

final class AttributeDefinitionFetchAdapterTest extends TestCase
{
    private const EXAMPLE_NAME_PREFIX = 'Foo';
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 25;

    public function testFetch(): void
    {
        $expectedResults = [
            $this->createMock(AttributeDefinitionInterface::class),
            $this->createMock(AttributeDefinitionInterface::class),
            $this->createMock(AttributeDefinitionInterface::class),
        ];

        $attributeDefinitionService = $this->createMock(AttributeDefinitionServiceInterface::class);
        $query = new AttributeDefinitionQuery(
            new NameCriterion(self::EXAMPLE_NAME_PREFIX),
            null,
            self::EXAMPLE_LIMIT,
            self::EXAMPLE_OFFSET,
        );
        $attributeDefinitionService
            ->method('findAttributesDefinitions')
            ->with($query)
            ->willReturn($this->createAttributeDefinitionList($expectedResults, 3));

        $originalQuery = new AttributeDefinitionQuery(
            new NameCriterion(self::EXAMPLE_NAME_PREFIX),
            null,
            0,
            0,
        );

        $adapter = new AttributeDefinitionFetchAdapter($attributeDefinitionService, $originalQuery);

        self::assertSame(
            $expectedResults,
            iterator_to_array($adapter->fetch(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT))
        );

        // Input $query remains untouched
        self::assertSame(0, $originalQuery->getOffset());
        self::assertSame(0, $originalQuery->getLimit());
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface[] $definitions
     */
    private function createAttributeDefinitionList(
        array $definitions,
        int $totalCount
    ): AttributeDefinitionListInterface {
        $list = $this->createMock(AttributeDefinitionListInterface::class);
        $list->method('getTotalCount')->willReturn($totalCount);
        $list->method('getAttributeDefinitions')->willReturn($definitions);
        $list->method('getIterator')->willReturn(new ArrayIterator($definitions));

        return $list;
    }
}
