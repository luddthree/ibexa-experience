<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount\Service;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\CorporateAccount\Exception\ValidationFailedExceptionInterface;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Tests\Integration\CorporateAccount\IbexaKernelTestCase;

/**
 * @covers \Ibexa\Contracts\CorporateAccount\Service\MemberService
 */
final class MemberServiceTest extends IbexaKernelTestCase
{
    private CompanyService $companyService;

    private MemberService $memberService;

    private Company $company;

    private RoleService $roleService;

    public function setUp(): void
    {
        parent::setUp();

        $this->companyService = self::getCompanyService();
        $this->memberService = self::getMemberService();
        $this->roleService = self::getRoleService();

        self::setAdministratorUser();

        $this->company = $this->createCompany();
    }

    /**
     * @return iterable<string, array{string, string, string, string, string, string, string}>
     */
    public function getDataForTestCreateMember(): iterable
    {
        $rolesIdentifiers = self::getCorporateAccountConfiguration()
            ->getCorporateAccountsRolesIdentifiers();

        foreach ($rolesIdentifiers as $roleKey => $roleIdentifier) {
            yield "with role $roleKey" => [
                "$roleKey-johndoe",
                "$roleKey-johndoe@email.invalid",
                "{$roleKey}John123#",
                $roleIdentifier,
                "$roleIdentifier John",
                'Doe',
                '123 456 789',
            ];
        }
    }

    /**
     * @dataProvider getDataForTestCreateMember
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testCreateMember(
        string $login,
        string $email,
        string $password,
        string $roleIdentifier,
        string $firstName,
        string $lastName,
        string $phoneNumber
    ): void {
        $loadedMember = $this->createMember(
            $roleIdentifier,
            $login,
            $email,
            $password,
            $firstName,
            $lastName,
            $phoneNumber
        );
        self::assertSame($login, $loadedMember->getUser()->login);
        self::assertSame($email, $loadedMember->getUser()->email);
        self::assertNotEmpty($loadedMember->getUser()->passwordHash);
        self::assertSame($roleIdentifier, $loadedMember->getRole()->identifier);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testCreateMembersThrowsExceptionOnInvalidExistingRole(): void
    {
        $this->expectException(ValidationFailedExceptionInterface::class);
        $this->expectExceptionMessage(
            'Argument \'$role\' is invalid: "Member" is not a valid Corporate Account Role'
        );
        $this->createMember(
            // Member is actually Core Repository Role, not Corporate Account Role
            'Member',
            'invalid-member',
            'invalid-member@email.invalid',
            'Publish123#',
            'Invalid',
            'Member',
            '000000000'
        );
    }

    public function testSetMemberRoleThrowsExceptionOnInvalidExistingRole(): void
    {
        $member = $this->createMember(
            'Company admin',
            'jon_snow',
            'jon.snow@email.invalid',
            'Publish123#',
            'Jon',
            'Snow',
            '123456789'
        );
        $editorRole = $this->roleService->loadRoleByIdentifier('Editor');

        $this->expectException(ValidationFailedExceptionInterface::class);
        $this->expectExceptionMessage(
            'Argument \'$role\' is invalid: "Editor" is not a valid Corporate Account Role'
        );
        $this->memberService->setMemberRole($member, $editorRole);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testGetMembers(): void
    {
        $expectedMembers = $this->createMembersForTestGetMembers();

        $members = $this->memberService->getCompanyMembers(
            $this->company,
            null,
            [new SortClause\ContentId()]
        );
        self::assertCount(3, $members);
        foreach ($expectedMembers as $index => $expectedMember) {
            self::assertSame($expectedMember->getId(), $members[$index]->getId());
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testCountMembers(): void
    {
        $this->createMembersForTestGetMembers();

        self::assertSame(3, $this->memberService->countCompanyMembers($this->company));
    }

    private function createCompany(): Company
    {
        $company = $this->companyService->createCompany(
            $this->getTestCompanyCreateStruct()
        );
        $this->companyService->createCompanyMembersUserGroup($company);
        // reload
        $company = $this->companyService->getCompany($company->getId());

        // sanity check
        self::assertCount(0, $this->memberService->getCompanyMembers($company));

        return $company;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    private function createMember(
        string $roleIdentifier,
        string $login,
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        string $phoneNumber
    ): Member {
        $role = $this->roleService->loadRoleByIdentifier($roleIdentifier);
        $memberCreateStruct = $this->memberService->newMemberCreateStruct(
            $login,
            $email,
            $password
        );
        $memberCreateStruct->setField('first_name', $firstName);
        $memberCreateStruct->setField('last_name', $lastName);
        $memberCreateStruct->setField('phone_number', $phoneNumber);
        $member = $this->memberService->createMember(
            $this->company,
            $memberCreateStruct,
            $role
        );

        return $this->memberService->getMember($member->getId(), $this->company);
    }

    /**
     * @return array<\Ibexa\Contracts\CorporateAccount\Values\Member>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    private function createMembersForTestGetMembers(): array
    {
        $rolesIdentifiers = self::getCorporateAccountConfiguration()
            ->getCorporateAccountsRolesIdentifiers();

        return [
            $this->createMember(
                $rolesIdentifiers['admin'],
                'admin1',
                'admin1@email.invalid',
                'Publish123#',
                'Admin',
                'Smith',
                '134'
            ),
            $this->createMember(
                $rolesIdentifiers['buyer'],
                'buyer1',
                'buyer1@email.invalid',
                'Publish123#',
                'Buyer 1',
                'Smith',
                '212'
            ),
            $this->createMember(
                $rolesIdentifiers['buyer'],
                'buyer2',
                'buyer2@email.invalid',
                'Publish123#',
                'Buyer 2',
                'Smith',
                '313'
            ),
        ];
    }
}
