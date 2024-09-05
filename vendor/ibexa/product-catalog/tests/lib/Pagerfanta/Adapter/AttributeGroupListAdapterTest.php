<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroupList;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\AttributeGroupListAdapter;
use PHPUnit\Framework\TestCase;

final class AttributeGroupListAdapterTest extends TestCase
{
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 100;
    private const EXAMPLE_TOTAL_COUNT = 100;

    /** @var \Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private AttributeGroupServiceInterface $attributeGroupService;

    private AttributeGroupListAdapter $adapter;

    protected function setUp(): void
    {
        $this->attributeGroupService = $this->createMock(AttributeGroupServiceInterface::class);
        $this->adapter = new AttributeGroupListAdapter(
            $this->attributeGroupService,
            new AttributeGroupQuery('foo')
        );
    }

    public function testGetSlice(): void
    {
        $expectedList = new AttributeGroupList();

        $this->attributeGroupService
            ->method('findAttributeGroups')
            ->with(new AttributeGroupQuery('foo', self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT))
            ->willReturn($expectedList);

        self::assertSame(
            $expectedList,
            $this->adapter->getSlice(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT)
        );
    }

    public function testGetNbResults(): void
    {
        $this->attributeGroupService
            ->method('findAttributeGroups')
            ->with(new AttributeGroupQuery('foo', 0, 0))
            ->willReturn(new AttributeGroupList(
                [/* Not important for this test case */],
                self::EXAMPLE_TOTAL_COUNT
            ));

        self::assertEquals(
            self::EXAMPLE_TOTAL_COUNT,
            $this->adapter->getNbResults()
        );
    }
}
