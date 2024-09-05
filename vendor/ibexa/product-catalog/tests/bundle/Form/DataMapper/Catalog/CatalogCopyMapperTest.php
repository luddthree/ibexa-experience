<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataMapper\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogCopyData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\Catalog\CatalogCopyMapper;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataMapper\Catalog\CatalogCreateMapper
 */
final class CatalogCopyMapperTest extends AbstractCatalogMapperTest
{
    public function testMap(): void
    {
        $catalog = $this->createMock(CatalogInterface::class);
        $catalog
            ->method('getId')
            ->willReturn(self::CATALOG_ID);
        $data = new CatalogCopyData($catalog, self::CATALOG_IDENTIFIER);

        $mapper = new CatalogCopyMapper();
        $result = $mapper->mapToStruct($data);

        self::assertSame(self::CATALOG_IDENTIFIER, $result->getIdentifier());
        self::assertSame(self::CATALOG_ID, $result->getSourceId());
        self::assertNull($result->getCreatorId());
    }
}
