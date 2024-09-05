<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

trait ProductDatabaseAssertionTrait
{
    protected static function getProductsCount(): int
    {
        $connection = self::getDoctrineConnection();
        $qb = $connection->createQueryBuilder();
        $countExpr = $connection->getDatabasePlatform()->getCountExpression('DISTINCT(ibexa_product_specification.content_id)');

        $result = $qb->select($countExpr)
            ->from('ibexa_product_specification')
            ->execute()
            ->fetchOne();

        self::assertNotFalse($result);

        return (int) $result;
    }

    protected static function assertCountProductsInDatabaseTable(int $expected): void
    {
        $count = self::getProductsCount();
        self::assertSame($expected, $count);
    }

    protected static function getContentProductsCount(): int
    {
        $connection = self::getDoctrineConnection();
        $qb = $connection->createQueryBuilder();
        $countExpr = $connection->getDatabasePlatform()->getCountExpression('DISTINCT(ibexa_product_specification.content_id)');

        $result = $qb->select($countExpr)
            ->from('ibexa_product_specification')
            ->join(
                'ibexa_product_specification',
                'ezcontentobject_attribute',
                'ezcontentobject_attribute',
                (string)$qb->expr()->and(
                    $qb->expr()->eq('ezcontentobject_attribute.contentobject_id', 'ibexa_product_specification.content_id'),
                    $qb->expr()->eq('ezcontentobject_attribute.version', 'ibexa_product_specification.version_no'),
                ),
            )
            ->execute()
            ->fetchOne();

        self::assertNotFalse($result);

        return (int) $result;
    }
}
