<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount\Service;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\CorporateAccount\Service\SalesRepresentativesService;
use Ibexa\Tests\Integration\CorporateAccount\IbexaKernelTestCase;

/**
 * @covers \Ibexa\Contracts\CorporateAccount\Service\SalesRepresentativesService
 * @covers \Ibexa\CorporateAccount\SalesRepresentativesService
 */
final class SalesRepresentativesServiceTest extends IbexaKernelTestCase
{
    public const PARENT_USER_GROUP_ID = 4;

    private SalesRepresentativesService $salesRepresentativesService;

    private UserService $userService;

    public function setUp(): void
    {
        parent::setUp();

        $this->userService = self::getUserService();
        $this->salesRepresentativesService = self::getSalesRepresentativesService();

        self::setAdministratorUser();
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testGetAllPaginated(): void
    {
        $salesRepresentativesUserGroup = $this->createSalesRepresentativesParentGroup();

        /** @var array<\Ibexa\Contracts\Core\Repository\Values\User\User> $users */
        $users = [];
        for ($i = 0; $i < 10; ++$i) {
            $users[] = $this->createUser("sales-rep$i", $salesRepresentativesUserGroup);
        }

        $this->assertSalesRepresentativesListSlice($users, 0, 5);
        $this->assertSalesRepresentativesListSlice($users, 5, 5);
        $this->assertSalesRepresentativesListSlice($users, 0, 10);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testGetAllNested(): void
    {
        $userService = self::getUserService();

        $salesRepresentativesUserGroup = $this->createSalesRepresentativesParentGroup();

        $nestedGroup01 = $this->createUserGroup(
            'Sales France',
            $salesRepresentativesUserGroup->id
        );
        $nestedGroup02 = $this->createUserGroup(
            'Sales Poland',
            $salesRepresentativesUserGroup->id
        );
        $evenMoreNestedGroup = $this->createUserGroup(
            'Sales Poland Cracow',
            $salesRepresentativesUserGroup->id
        );

        $salesRepresentatives = [
            $this->createUser('sales-rep-01-fr', $nestedGroup01),
            $this->createUser('sales-rep-02-fr', $nestedGroup01),
            $this->createUser('sales-rep-03-pl', $nestedGroup02),
            $this->createUser('sales-rep-04-pl', $evenMoreNestedGroup),
        ];
        // create a decoy that should not be matched by Criteria
        $this->createUser('regular-user', $userService->loadUserGroup(self::PARENT_USER_GROUP_ID));

        $this->assertSalesRepresentativesListSlice($salesRepresentatives, 0, 6);
    }

    /**
     * @param array<int, \Ibexa\Contracts\Core\Repository\Values\User\User> $users
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    private function assertSalesRepresentativesListSlice(array $users, int $offset, int $limit): void
    {
        $users = array_slice($users, $offset, $limit);
        $usersCount = count($users);
        $usersFound = $usersCount === 0; // will remain false if there are users to be found, but were not found
        foreach ($this->salesRepresentativesService->getAll($offset, $limit) as $idx => $user) {
            $usersFound = true;
            self::assertSame(
                $users[$idx]->getUserId(),
                $user->getUserId(),
                sprintf(
                    'Failed asserting user at the offset %d is as expected. Expecting [%d] %s, found [%d] %s',
                    $idx,
                    $users[$idx]->getUserId(),
                    $users[$idx]->login,
                    $user->getUserId(),
                    $user->login
                )
            );
        }

        self::assertTrue($usersFound, sprintf(
            'Expected to find %d users at offset %d and limit %d but found none',
            $usersCount,
            $offset,
            $limit
        ));
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    private function createUserGroup(string $groupName, int $parentGroupId, ?string $locationRemoteId = null): UserGroup
    {
        $userService = self::getUserService();

        $userGroupCreateStruct = $userService->newUserGroupCreateStruct('eng-US');
        $userGroupCreateStruct->setField('name', $groupName);

        $userGroup = $userService->createUserGroup($userGroupCreateStruct, $userService->loadUserGroup($parentGroupId));
        // createUserGroup API doesn't allow setting Location remote ID
        if (null !== $locationRemoteId) {
            $this->updateUserGroupLocationRemoteId($locationRemoteId, $userGroup);
        }

        return $userGroup;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    private function createUser(string $login, UserGroup $userGroup): User
    {
        $userCreateStruct = $this->userService->newUserCreateStruct(
            $login,
            "$login@email.invalid",
            'Publish123#',
            'eng-US'
        );
        $userCreateStruct->setField('first_name', $login);
        $userCreateStruct->setField('last_name', $login);

        return $this->userService->createUser($userCreateStruct, [$userGroup]);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    private function createSalesRepresentativesParentGroup(): UserGroup
    {
        $configuration = self::getCorporateAccountConfiguration();

        return $this->createUserGroup(
            'Sales Representatives',
            self::PARENT_USER_GROUP_ID,
            $configuration->getSalesRepUserGroupRemoteId()
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function updateUserGroupLocationRemoteId(string $locationRemoteId, UserGroup $userGroup): void
    {
        $locationService = self::getLocationService();

        $locationUpdateStruct = $locationService->newLocationUpdateStruct();
        $locationUpdateStruct->remoteId = $locationRemoteId;
        $mainLocation = $userGroup->getVersionInfo()->getContentInfo()->getMainLocation();
        self::assertNotNull($mainLocation);
        $locationService->updateLocation(
            $mainLocation,
            $locationUpdateStruct
        );
    }
}
