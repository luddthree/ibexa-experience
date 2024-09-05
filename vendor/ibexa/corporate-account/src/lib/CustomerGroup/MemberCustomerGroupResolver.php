<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\CustomerGroup;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value as CustomerGroupValue;

final class MemberCustomerGroupResolver implements CustomerGroupResolverInterface
{
    private MemberResolver $memberResolver;

    private MemberService $memberService;

    private CompanyService $companyService;

    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(
        MemberResolver $memberResolver,
        MemberService $memberService,
        CompanyService $companyService,
        CustomerGroupServiceInterface $customerGroupService
    ) {
        $this->customerGroupService = $customerGroupService;
        $this->memberResolver = $memberResolver;
        $this->memberService = $memberService;
        $this->companyService = $companyService;
    }

    public function resolveCustomerGroup(?User $user = null): ?CustomerGroupInterface
    {
        try {
            $member = $user !== null ? $this->getMemberFromUser($user) : $this->getCurrentMember();
        } catch (InvalidArgumentException $exception) {
            // User is not a member or is not assigned to any company
            return null;
        }

        $company = $member->getCompany();

        foreach ($company->getContent()->getFields() as $field) {
            if ($field->value instanceof CustomerGroupValue && $field->value->getId() !== null) {
                return $this->customerGroupService->getCustomerGroup(
                    $field->value->getId()
                );
            }
        }

        return null;
    }

    private function getCurrentMember(): Member
    {
        return $this->memberResolver->getCurrentMember();
    }

    private function getMemberFromUser(User $user): Member
    {
        $assignments = $this->memberService->getMemberAssignmentsByMemberId($user->id);

        $assignment = reset($assignments);

        if (false === $assignment) {
            throw new InvalidArgumentException('$userId', 'Member does not belong to any company');
        }

        $company = $this->companyService->getCompany($assignment->getCompanyId());

        return $this->memberService->getMember(
            $user->getUserId(),
            $company
        );
    }
}
