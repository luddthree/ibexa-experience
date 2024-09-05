<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataMapper\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogCreateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\Catalog\CatalogCreateMapper;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\MatchAll;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataMapper\Catalog\CatalogCreateMapper
 */
final class CatalogCreateMapperTest extends AbstractCatalogMapperTest
{
    public function testMap(): void
    {
        $criteria = new LogicalAnd([]);

        $data = new CatalogCreateData();
        $data->setIdentifier(self::CATALOG_IDENTIFIER);
        $data->setLanguage($this->getTestLanguage());
        $data->setName(self::CATALOG_NAME);
        $data->setDescription(self::CATALOG_DESCRIPTION);
        $data->setCriteria($criteria);

        $mapper = new CatalogCreateMapper();
        $result = $mapper->mapToStruct($data);

        self::assertSame(self::CATALOG_IDENTIFIER, $result->getIdentifier());
        self::assertNull($result->getCreatorId());
        self::assertSame(
            self::CATALOG_NAME,
            $result->getName(self::LANGUAGE_CODE)
        );
        self::assertSame(
            self::CATALOG_DESCRIPTION,
            $result->getDescription(self::LANGUAGE_CODE)
        );
        self::assertEquals($criteria, $result->getCriterion());
    }

    public function testMapDataWithEmptyCriteria(): void
    {
        $data = new CatalogCreateData();
        $data->setIdentifier(self::CATALOG_IDENTIFIER);
        $data->setLanguage($this->getTestLanguage());
        $data->setName(self::CATALOG_NAME);
        $data->setDescription(self::CATALOG_DESCRIPTION);
        $data->setCriteria(null);

        $mapper = new CatalogCreateMapper();
        $result = $mapper->mapToStruct($data);

        self::assertSame(self::CATALOG_IDENTIFIER, $result->getIdentifier());
        self::assertNull($result->getCreatorId());
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
