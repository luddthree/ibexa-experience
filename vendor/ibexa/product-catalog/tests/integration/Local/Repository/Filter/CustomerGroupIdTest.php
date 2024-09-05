<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Filter;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentId;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\CustomerGroupId;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

final class CustomerGroupIdTest extends IbexaKernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();
    }

    public function testCustomerGroupIdCriterion(): void
    {
        $customerGroup = self::getCustomerGroupService()->getCustomerGroupByIdentifier('customer_group_1');
        self::assertNotNull($customerGroup, 'Unable to find "customer_group_1" customer group');

        $anonymousUser = self::getUserService()->loadUserByLogin('anonymous');
        $adminUser = self::getUserService()->loadUserByLogin('admin');

        $filter = new Filter(new CustomerGroupId($customerGroup->getId()), [
            new ContentId(),
        ]);

        $this->assignCustomerGroup($anonymousUser, $customerGroup);
        $this->assignCustomerGroup($adminUser, $customerGroup);

        $results = self::getContentService()->find($filter);

        self::assertEquals(2, $results->getTotalCount());

        $users = iterator_to_array($results);

        $this->assertUserEquals($anonymousUser, $users[0]);
        $this->assertUserEquals($adminUser, $users[1]);
    }

    private function assignCustomerGroup(User $user, CustomerGroupInterface $customerGroup): void
    {
        $userService = self::getUserService();

        $updateStruct = $userService->newUserUpdateStruct();
        $updateStruct->contentUpdateStruct = self::getContentService()->newContentUpdateStruct();
        $updateStruct->contentUpdateStruct->setField(
            'customer_group',
            new Value($customerGroup->getId())
        );

        $userService->updateUser($user, $updateStruct);
    }

    private function assertUserEquals(User $user, Content $searchHit): void
    {
        self::assertEquals($user->getUserId(), $searchHit->id);
    }
}
