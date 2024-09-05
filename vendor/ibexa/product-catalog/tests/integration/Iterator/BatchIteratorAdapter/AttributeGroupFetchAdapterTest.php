<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Iterator\BatchIteratorAdapter;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\AttributeGroupFetchAdapter;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use PHPUnit\Framework\TestCase;

final class AttributeGroupFetchAdapterTest extends TestCase
{
    private const EXAMPLE_NAME_PREFIX = 'Foo';
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 25;

    public function testFetch(): void
    {
        $expectedResults = [
            $this->createMock(AttributeGroupInterface::class),
            $this->createMock(AttributeGroupInterface::class),
            $this->createMock(AttributeGroupInterface::class),
        ];

        $attributeGroupService = $this->createMock(AttributeGroupServiceInterface::class);
        $attributeGroupService
            ->method('findAttributeGroups')
            ->with(new AttributeGroupQuery(
                self::EXAMPLE_NAME_PREFIX,
                self::EXAMPLE_OFFSET,
                self::EXAMPLE_LIMIT
            ))
            ->willReturn($this->createAttributeGroupList($expectedResults));

        $originalQuery = new AttributeGroupQuery(self::EXAMPLE_NAME_PREFIX, 0, 0);

        $adapter = new AttributeGroupFetchAdapter($attributeGroupService, $originalQuery);

        self::assertSame(
            $expectedResults,
            iterator_to_array($adapter->fetch(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT))
        );

        // Input $query remains untouched
        self::assertSame(0, $originalQuery->getOffset());
        self::assertSame(0, $originalQuery->getLimit());
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface[] $groups
     */
    private function createAttributeGroupList(array $groups): AttributeGroupListInterface
    {
        $list = $this->createMock(AttributeGroupListInterface::class);
        $list->method('getIterator')->willReturn(new ArrayIterator($groups));

        return $list;
    }
}
