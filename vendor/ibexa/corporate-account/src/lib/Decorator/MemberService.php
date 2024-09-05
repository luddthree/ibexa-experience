<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Decorator;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\RoleAssignment;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\CorporateAccount\Service\MemberService as MemberServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\CorporateAccount\Values\MemberAssignment;
use Ibexa\Contracts\CorporateAccount\Values\MemberCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\MemberUpdateStruct;

abstract class MemberService implements MemberServiceInterface
{
    protected MemberServiceInterface $innerService;

    public function __construct(
        MemberServiceInterface $innerService
    ) {
        $this->innerService = $innerService;
    }

    public function getMember(
        int $memberId,
        Company $company
    ): Member {
        return $this->innerService->getMember($memberId, $company);
    }

    public function getCompanyMembers(
        Company $company,
        ?Criterion $filter = null,
        array $sortClauses = [],
        ?int $limit = 25,
        int $offset = 0
    ): array {
        return $this->innerService->getCompanyMembers(
            $company,
            $filter,
            $sortClauses,
            $limit,
            $offset
        );
    }

    public function countCompanyMembers(
        Company $company,
        ?Criterion $filter = null
    ): int {
        return $this->innerService->countCompanyMembers(
            $company,
            $filter
        );
    }

    public function getMemberAssignment(
        Member $member,
        Company $company
    ): MemberAssignment {
        return $this->innerService->getMemberAssignment($member, $company);
    }

    public function getMemberAssignmentByUser(
        User $user,
        Company $company
    ): MemberAssignment {
        return $this->innerService->getMemberAssignmentByUser($user, $company);
    }

    public function getMemberAssignments(Member $member): iterable
    {
        return $this->innerService->getMemberAssignments($member);
    }

    public function getMemberAssignmentsByMemberId(int $memberId): iterable
    {
        return $this->innerService->getMemberAssignmentsByMemberId($memberId);
    }

    public function getRoleAssignmentByUser(
        User $user,
        Role $role,
        string $locationPath
    ): RoleAssignment {
        return $this->innerService->getRoleAssignmentByUser($user, $role, $locationPath);
    }

    public function getRoleAssignment(Member $member): RoleAssignment
    {
        return $this->innerService->getRoleAssignment($member);
    }

    public function createMember(
        Company $company,
        MemberCreateStruct $memberCreateStruct,
        Role $role
    ): Member {
        return $this->innerService->createMember($company, $memberCreateStruct, $role);
    }

    public function updateMember(
        Member $member,
        MemberUpdateStruct $memberUpdateStruct
    ): Member {
        return $this->innerService->updateMember($member, $memberUpdateStruct);
    }

    public function deleteMember(Member $member): void
    {
        $this->innerService->deleteMember($member);
    }

    public function setMemberRole(
        Member $member,
        Role $role
    ): void {
        $this->innerService->setMemberRole($member, $role);
    }

    public function getCompanyContact(Company $company): ?Member
    {
        return $this->innerService->getCompanyContact($company);
    }

    public function newMemberCreateStruct(
        string $login,
        string $email,
        string $password,
        ?ContentType $contentType = null
    ): MemberCreateStruct {
        return $this->innerService->newMemberCreateStruct(
            $login,
            $email,
            $password,
            $contentType
        );
    }

    public function newMemberUpdateStruct(): MemberUpdateStruct
    {
        return $this->innerService->newMemberUpdateStruct();
    }
}
