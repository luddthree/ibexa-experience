<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;

abstract class BaseProductPriceServiceTest extends BaseServiceTest
{
    protected const EXAMPLE_PRODUCT_CODE = 'PRICE_INTEGRATION_TEST';
    protected const EXAMPLE_PRODUCT_CODE2 = '0001';

    protected ProductPriceServiceInterface $productPriceService;

    protected function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();

        self::executeMigration('product_price_service_setup.yaml');
        self::ensureSearchIndexIsUpdated();

        $this->productPriceService = self::getProductPriceService();
    }

    protected function tearDown(): void
    {
        self::executeMigration('product_price_service_teardown.yaml');
        self::ensureSearchIndexIsUpdated();
    }
}
