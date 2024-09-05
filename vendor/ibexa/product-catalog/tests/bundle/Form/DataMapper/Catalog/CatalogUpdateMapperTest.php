<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataMapper\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\Catalog\CatalogUpdateMapper;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\MatchAll;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataMapper\Catalog\CatalogUpdateMapper
 */
final class CatalogUpdateMapperTest extends AbstractCatalogMapperTest
{
    public function testMap(): void
    {
        $criteria = new LogicalAnd([]);
        $data = new CatalogUpdateData(
            self::CATALOG_ID,
            $this->getTestLanguage(),
            self::CATALOG_IDENTIFIER,
            self::CATALOG_NAME,
            self::CATALOG_DESCRIPTION,
            $criteria
        );

        $mapper = new CatalogUpdateMapper();
        $result = $mapper->mapToStruct($data);

        self::assertSame(self::CATALOG_ID, $result->getId());
        self::assertSame(self::CATALOG_IDENTIFIER, $result->getIdentifier());
        self::assertSame(
            self::CATALOG_NAME,
            $result->getName(self::LANGUAGE_CODE)
        );
        self::assertSame(
            self::CATALOG_DESCRIPTION,
            $result->getDescription(self::LANGUAGE_CODE)
        );
        self::assertSame($criteria, $result->getCriterion());
    }

    public function testMapDataWithEmptyCriteria(): void
    {
        $data = new CatalogUpdateData(
            self::CATALOG_ID,
            $this->getTestLanguage(),
            self::CATALOG_IDENTIFIER,
            self::CATALOG_NAME,
            self::CATALOG_DESCRIPTION,
            null
        );

        $mapper = new CatalogUpdateMapper();
        $result = $mapper->mapToStruct($data);

        self::assertSame(self::CATALOG_ID, $result->getId());
        self::assertSame(self::CATALOG_IDENTIFIER, $result->getIdentifier());
        self::assertSame(
            self::CATALOG_NAME,
            $result->getName(self::LANGUAGE_CODE)
        );
        self::assertSame(
            self::CATALOG_DESCRIPTION,
            $result->getDescription(self::LANGUAGE_CODE)
        );
        self::assertEquals(
            new LogicalAnd([new MatchAll()]),
            $result->getCriterion()
        );
    }
}
