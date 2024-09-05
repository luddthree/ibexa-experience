<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\CustomerGroupFixture;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\CustomerGroupService
 *
 * @group customer-group-service
 */
final class CustomerGroupServiceAuthorizationTest extends BaseCustomerGroupServiceTest
{
    public function testCreateThrowsUnauthorizedException(): void
    {
        $service = self::getCustomerGroupService();

        $struct = self::getCustomerGroupCreateStruct();

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'create\' \'customer_group\'/');

        $service->createCustomerGroup($struct);
    }

    public function testFindCustomerGroupsThrowsUnauthorizedException(): void
    {
        self::setAnonymousUser();

        $customerGroupList = self::getCustomerGroupService()->findCustomerGroups();

        self::assertEquals(0, $customerGroupList->getTotalCount());
    }

    public function testFindByIdentifierThrowsUnauthorizedException(): void
    {
        $service = self::getCustomerGroupService();

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'view\' \'customer_group\'/');

        $service->getCustomerGroupByIdentifier('foo');
    }

    public function testGetCustomerGroupThrowsUnauthorizedException(): void
    {
        $service = self::getCustomerGroupService();

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'view\' \'customer_group\'/');

        $service->getCustomerGroup(CustomerGroupFixture::FIXTURE_ENTRY_ID);
    }

    public function testUpdateThrowsUnauthorizedException(): void
    {
        $service = self::getCustomerGroupService();

        $struct = self::getCustomerGroupUpdateStruct();

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'customer_group\'/');

        $service->updateCustomerGroup($struct);
    }

    /**
     * @depends Ibexa\Tests\Integration\ProductCatalog\Local\Repository\CustomerGroupServiceTest::testGetCustomerGroup
     */
    public function testDeleteThrowsUnauthorizedException(CustomerGroupInterface $customerGroup): void
    {
        $service = self::getCustomerGroupService();

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'delete\' \'customer_group\'/');

        $service->deleteCustomerGroup($customerGroup);
    }
}
