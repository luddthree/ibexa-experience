<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Service;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\RoleAssignment;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\CorporateAccount\Values\MemberAssignment;
use Ibexa\Contracts\CorporateAccount\Values\MemberCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\MemberUpdateStruct;

interface MemberService
{
    public const DEFAULT_COMPANY_MEMBERS_LIST_LIMIT = 25;

    public function getMember(int $memberId, Company $company): Member;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     *
     * @return \Ibexa\Contracts\CorporateAccount\Values\Member[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getCompanyMembers(
        Company $company,
        ?Criterion $filter = null,
        array $sortClauses = [],
        ?int $limit = self::DEFAULT_COMPANY_MEMBERS_LIST_LIMIT,
        int $offset = 0
    ): array;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function countCompanyMembers(Company $company, ?Criterion $filter = null): int;

    public function getMemberAssignment(Member $member, Company $company): MemberAssignment;

    public function getMemberAssignmentByUser(User $user, Company $company): MemberAssignment;

    /**
     * @return iterable<\Ibexa\Contracts\CorporateAccount\Values\MemberAssignment>
     */
    public function getMemberAssignments(Member $member): iterable;

    /**
     * @return iterable<\Ibexa\Contracts\CorporateAccount\Values\MemberAssignment>
     */
    public function getMemberAssignmentsByMemberId(int $memberId): iterable;

    public function getRoleAssignmentByUser(User $user, Role $role, string $locationPath): RoleAssignment;

    public function getRoleAssignment(Member $member): RoleAssignment;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\CorporateAccount\Exception\ValidationFailedExceptionInterface
     */
    public function createMember(Company $company, MemberCreateStruct $memberCreateStruct, Role $role): Member;

    public function updateMember(Member $member, MemberUpdateStruct $memberUpdateStruct): Member;

    public function deleteMember(Member $member): void;

    public function setMemberRole(Member $member, Role $role): void;

    public function getCompanyContact(Company $company): ?Member;

    public function newMemberCreateStruct(
        string $login,
        string $email,
        string $password,
        ?ContentType $contentType = null
    ): MemberCreateStruct;

    public function newMemberUpdateStruct(): MemberUpdateStruct;
}
