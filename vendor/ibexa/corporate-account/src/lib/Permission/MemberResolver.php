<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Permission;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver as MemberResolverInterface;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Member;

final class MemberResolver implements MemberResolverInterface
{
    private PermissionResolver $permissionResolver;

    private CompanyService $companyService;

    private MemberService $memberService;

    public function __construct(
        PermissionResolver $permissionResolver,
        CompanyService $companyService,
        MemberService $memberService
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->companyService = $companyService;
        $this->memberService = $memberService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function getCurrentMember(): Member
    {
        $userReference = $this->permissionResolver->getCurrentUserReference();

        $memberAssignments = $this->memberService->getMemberAssignmentsByMemberId($userReference->getUserId());
        $memberAssignment = reset($memberAssignments);

        if (false === $memberAssignment) {
            throw new InvalidArgumentException('$userId', 'Member does not belong to any company');
        }

        $company = $this->companyService->getCompany($memberAssignment->getCompanyId());

        return $this->memberService->getMember(
            $userReference->getUserId(),
            $company
        );
    }
}
