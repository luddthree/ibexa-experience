<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\CustomerGroup;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\User\User as UserValue;
use Ibexa\CorporateAccount\CustomerGroup\MemberCustomerGroupResolver;
use Ibexa\CorporateAccount\Values\MemberAssignment;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value as CustomerGroupValue;
use PHPUnit\Framework\TestCase;

final class MemberCustomerGroupResolverTest extends TestCase
{
    private const EXAMPLE_MEMBER_ID = 1;
    private const EXAMPLE_CUSTOMER_GROUP_ID = 2;
    private const EXAMPLE_COMPANY_ID = 3;

    /** @var \Ibexa\Contracts\CorporateAccount\Permission\MemberResolver|\PHPUnit\Framework\MockObject\MockObject */
    private MemberResolver $memberResolver;

    /** @var \Ibexa\Contracts\CorporateAccount\Service\MemberService|\PHPUnit\Framework\MockObject\MockObject */
    private MemberService $memberService;

    /** @var \Ibexa\Contracts\CorporateAccount\Service\CompanyService|\PHPUnit\Framework\MockObject\MockObject */
    private CompanyService $companyService;

    /** @var \Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CustomerGroupServiceInterface $customerGroupService;

    private MemberCustomerGroupResolver $customerGroupResolver;

    protected function setUp(): void
    {
        $this->memberResolver = $this->createMock(MemberResolver::class);
        $this->memberService = $this->createMock(MemberService::class);
        $this->companyService = $this->createMock(CompanyService::class);
        $this->customerGroupService = $this->createMock(CustomerGroupServiceInterface::class);

        $this->customerGroupResolver = new MemberCustomerGroupResolver(
            $this->memberResolver,
            $this->memberService,
            $this->companyService,
            $this->customerGroupService
        );
    }

    public function testResolveCustomerGroupForCurrentMember(): void
    {
        $user = $this->createMock(User::class);
        $companyContent = $this->createMock(Content::class);
        $companyContent->method('getFields')->willReturn(
            [
                new Field(['value' => new CustomerGroupValue(self::EXAMPLE_CUSTOMER_GROUP_ID)]),
            ]
        );
        $company = new Company(
            $companyContent
        );

        $member = new Member(
            $user,
            $company,
            $this->createMock(Role::class)
        );

        $this->memberResolver
            ->method('getCurrentMember')
            ->willReturn($member);

        $expectedCustomerGroup = $this->createMock(CustomerGroupInterface::class);

        $this->customerGroupService
            ->method('getCustomerGroup')
            ->with(self::EXAMPLE_CUSTOMER_GROUP_ID)
            ->willReturn($expectedCustomerGroup);

        self::assertEquals(
            $expectedCustomerGroup,
            $this->customerGroupResolver->resolveCustomerGroup()
        );
    }

    public function testResolveCustomerGroupForGivenUser(): void
    {
        $this
            ->memberService
            ->method('getMemberAssignmentsByMemberId')
            ->with(self::EXAMPLE_MEMBER_ID)
            ->willReturn([new MemberAssignment(
                1,
                self::EXAMPLE_MEMBER_ID,
                'example_role',
                144,
                self::EXAMPLE_COMPANY_ID,
                155
            )]);

        $companyContent = $this->createMock(Content::class);
        $companyContent->method('getFields')->willReturn(
            [
                new Field(['value' => new CustomerGroupValue(self::EXAMPLE_CUSTOMER_GROUP_ID)]),
            ]
        );

        $company = new Company(
            $companyContent
        );

        $this
            ->companyService
            ->method('getCompany')
            ->with(self::EXAMPLE_COMPANY_ID)
            ->willReturn($company);

        $user = $this->getUser();

        $member = new Member(
            $user,
            $company,
            $this->createMock(Role::class)
        );

        $this
            ->memberService
            ->method('getMember')
            ->with(self::EXAMPLE_MEMBER_ID, $company)
            ->willReturn($member);

        $expectedCustomerGroup = $this->createMock(CustomerGroupInterface::class);

        $this->customerGroupService
            ->method('getCustomerGroup')
            ->with(self::EXAMPLE_CUSTOMER_GROUP_ID)
            ->willReturn($expectedCustomerGroup);

        self::assertEquals(
            $expectedCustomerGroup,
            $this->customerGroupResolver->resolveCustomerGroup($user)
        );
    }

    public function testResolveCustomerGroupFailureWhenUserIsNotAMember(): void
    {
        $user = $this->getUser();

        $this
            ->memberService
            ->method('getMemberAssignmentsByMemberId')
            ->with(self::EXAMPLE_MEMBER_ID)
            ->willReturn([]);

        self::assertNull($this->customerGroupResolver->resolveCustomerGroup($user));
    }

    public function testResolveCustomerGroupFailureWhenCompanyWithoutCustomerGroup(): void
    {
        $this
            ->memberService
            ->method('getMemberAssignmentsByMemberId')
            ->with(self::EXAMPLE_MEMBER_ID)
            ->willReturn([new MemberAssignment(
                1,
                self::EXAMPLE_MEMBER_ID,
                'example_role',
                144,
                self::EXAMPLE_COMPANY_ID,
                155
            )]);

        $companyContent = $this->createMock(Content::class);
        $companyContent->method('getFields')->willReturn(
            [
                new Field(['value' => new CustomerGroupValue(null)]),
            ]
        );
        $company = new Company(
            $companyContent
        );

        $this
            ->companyService
            ->method('getCompany')
            ->with(self::EXAMPLE_COMPANY_ID)
            ->willReturn($company);

        $user = $this->getUser();

        $member = new Member(
            $user,
            $company,
            $this->createMock(Role::class)
        );

        $this
            ->memberService
            ->method('getMember')
            ->with(self::EXAMPLE_MEMBER_ID, $company)
            ->willReturn($member);

        self::assertNull($this->customerGroupResolver->resolveCustomerGroup($user));
    }

    private function getUser(): UserValue
    {
        return new UserValue([
            'content' => new Content([
                'versionInfo' => new VersionInfo([
                    'contentInfo' => new ContentInfo([
                        'id' => self::EXAMPLE_MEMBER_ID,
                    ]),
                ]),
            ]),
        ]);
    }
}
