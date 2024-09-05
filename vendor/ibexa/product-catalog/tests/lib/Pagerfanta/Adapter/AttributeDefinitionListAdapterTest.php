<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionList;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\AttributeDefinitionListAdapter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Pagerfanta\Adapter\AttributeDefinitionListAdapter
 */
final class AttributeDefinitionListAdapterTest extends TestCase
{
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 100;
    private const EXAMPLE_TOTAL_COUNT = 100;

    /** @var \Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    private AttributeDefinitionListAdapter $adapter;

    protected function setUp(): void
    {
        $this->attributeDefinitionService = $this->createMock(AttributeDefinitionServiceInterface::class);
        $query = new AttributeDefinitionQuery();
        $this->adapter = new AttributeDefinitionListAdapter(
            $this->attributeDefinitionService,
            $query
        );
    }

    public function testGetSlice(): void
    {
        $expectedList = new AttributeDefinitionList();

        $query = new AttributeDefinitionQuery(
            null,
            null,
            self::EXAMPLE_LIMIT,
            self::EXAMPLE_OFFSET,
        );
        $this->attributeDefinitionService
            ->method('findAttributesDefinitions')
            ->with($query)
            ->willReturn($expectedList);

        self::assertSame(
            $expectedList,
            $this->adapter->getSlice(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT)
        );
    }

    public function testGetNbResults(): void
    {
        $query = new AttributeDefinitionQuery(null, null, 0, 0);
        $this->attributeDefinitionService
            ->method('findAttributesDefinitions')
            ->with($query)
            ->willReturn(new AttributeDefinitionList(
                [/* Not important for this test case */],
                self::EXAMPLE_TOTAL_COUNT
            ));

        self::assertEquals(
            self::EXAMPLE_TOTAL_COUNT,
            $this->adapter->getNbResults()
        );
    }
}
