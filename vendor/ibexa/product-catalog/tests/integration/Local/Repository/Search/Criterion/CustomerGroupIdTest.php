<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
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
        self::ensureSearchIndexIsUpdated();
    }

    public function testCustomerGroupIdCriterion(): void
    {
        $customerGroup = self::getCustomerGroupService()->getCustomerGroupByIdentifier('customer_group_2');
        self::assertNotNull($customerGroup, 'Unable to find "customer_group_2" customer group');

        $anonymousUser = self::getUserService()->loadUserByLogin('anonymous');
        $adminUser = self::getUserService()->loadUserByLogin('admin');

        $searchService = self::getSearchService();

        $query = new Query();
        $query->filter = new CustomerGroupId($customerGroup->getId());

        self::assertEquals(0, $searchService->findContent($query)->totalCount);

        $this->assignCustomerGroup($anonymousUser, $customerGroup);
        $this->assignCustomerGroup($adminUser, $customerGroup);

        self::ensureSearchIndexIsUpdated();

        $searchResults = $searchService->findContent($query);

        self::assertEquals(2, $searchResults->totalCount);
        $this->assertUserEquals($anonymousUser, $searchResults->searchHits[0]);
        $this->assertUserEquals($adminUser, $searchResults->searchHits[1]);
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

    private function assertUserEquals(User $user, SearchHit $searchHit): void
    {
        self::assertInstanceOf(Content::class, $searchHit->valueObject);
        self::assertEquals($user->getUserId(), $searchHit->valueObject->id);
    }
}
