<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Test\ProductCatalog\Local\QueryType;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\ProductCatalog\Local\QueryType\CatalogQueryType;
use PHPUnit\Framework\TestCase;

final class CatalogQueryTypeTest extends TestCase
{
    private const EXAMPLE_IDENTIFIER = 'foo';

    /** @var \Ibexa\Contracts\ProductCatalog\CatalogServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CatalogServiceInterface $catalogService;

    private CatalogQueryType $queryType;

    protected function setUp(): void
    {
        $this->catalogService = $this->createMock(CatalogServiceInterface::class);
        $this->queryType = new CatalogQueryType($this->catalogService);
    }

    public function testGetQuery(): void
    {
        $criteria = $this->createMock(CriterionInterface::class);

        $catalog = $this->createMock(CatalogInterface::class);
        $catalog->method('getQuery')->willReturn($criteria);

        $this->catalogService
            ->method('getCatalogByIdentifier')
            ->with(self::EXAMPLE_IDENTIFIER)
            ->willReturn($catalog);

        $expectedQuery = new Query();
        $expectedQuery->filter = new ProductCriterionAdapter($criteria);

        self::assertEquals(
            $expectedQuery,
            $this->queryType->getQuery([
                'identifier' => self::EXAMPLE_IDENTIFIER,
            ])
        );
    }

    public function testGetSupportedParameters(): void
    {
        self::assertEquals(
            ['identifier'],
            $this->queryType->getSupportedParameters()
        );
    }
}
