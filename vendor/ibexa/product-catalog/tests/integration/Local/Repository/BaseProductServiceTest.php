<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\ProductService
 *
 * @group product-service
 */
abstract class BaseProductServiceTest extends IbexaKernelTestCase
{
    use ProductDatabaseAssertionTrait;

    protected const TEST_PRODUCT_TYPE_IDENTIFIER_TROUSERS = 'trousers';
    protected const TEST_PRODUCT_TYPE_IDENTIFIER_DRESS = 'dress';

    protected function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();
        self::ensureSearchIndexIsUpdated();
    }

    protected static function assertVersionNoIsSameInProductStorageForProductCode(int $versionNo, string $code): void
    {
        $connection = self::getDoctrineConnection();
        $qb = $connection->createQueryBuilder();
        $result = $qb->select('ibexa_product_specification.version_no')
            ->from('ibexa_product_specification')
            ->where($qb->expr()->eq('ibexa_product_specification.code', $qb->createPositionalParameter($code)))
            ->orderBy('ibexa_product_specification.version_no', 'DESC')
            ->setMaxResults(1)
            ->execute()
            ->fetchOne();

        self::assertNotFalse($result);
        self::assertSame($versionNo, (int) $result);
    }

    /**
     * @param array<string,mixed> $expectedAttributes
     */
    protected static function assertAttributesValue(array $expectedAttributes, ProductInterface $product): void
    {
        $actualAttributes = [];
        foreach ($product->getAttributes() as $attribute) {
            $actualAttributes[$attribute->getIdentifier()] = $attribute->getValue();
        }

        self::assertEquals($expectedAttributes, $actualAttributes);
    }
}
