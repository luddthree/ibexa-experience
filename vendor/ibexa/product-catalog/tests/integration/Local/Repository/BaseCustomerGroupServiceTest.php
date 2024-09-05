<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;
use Ibexa\ProductCatalog\Local\Repository\CustomerGroupService;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

abstract class BaseCustomerGroupServiceTest extends IbexaKernelTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();
    }

    protected static function getCustomerGroupService(): CustomerGroupService
    {
        return self::getServiceByClassName(CustomerGroupService::class);
    }

    /**
     * @param numeric-string $defaultPriceDiscountRate
     */
    protected static function getCustomerGroupCreateStruct(
        string $identifier = 'foo',
        string $name = 'Foo',
        string $description = 'Lorem Ipsum',
        string $defaultPriceDiscountRate = '0'
    ): CustomerGroupCreateStruct {
        return new CustomerGroupCreateStruct(
            $identifier,
            [
                2 => $name,
            ],
            [
                2 => $description,
            ],
            $defaultPriceDiscountRate,
        );
    }

    /**
     * @param numeric-string $defaultPriceDiscountRate
     */
    protected static function getCustomerGroupUpdateStruct(
        int $id = 42,
        string $identifier = 'bar',
        string $name = 'Bar',
        string $description = 'Luke, I\'m your father',
        string $defaultPriceDiscountRate = '0'
    ): CustomerGroupUpdateStruct {
        return new CustomerGroupUpdateStruct(
            $id,
            $identifier,
            [
                2 => $name,
            ],
            [
                2 => $description,
            ],
            $defaultPriceDiscountRate,
        );
    }
}
