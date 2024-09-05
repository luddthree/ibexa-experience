<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\ParamConverter;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class MemberParamConverter implements ParamConverterInterface
{
    public const PARAMETER_MEMBER = 'member';
    public const PARAMETER_MEMBER_ID = 'memberId';
    public const PARAMETER_COMPANY_ID = 'companyId';

    private CompanyService $companyService;

    private MemberService $memberService;

    public function __construct(
        CompanyService $companyService,
        MemberService $memberService
    ) {
        $this->companyService = $companyService;
        $this->memberService = $memberService;
    }

    public function apply(
        Request $request,
        ParamConverter $configuration
    ): bool {
        $memberId = $request->get(self::PARAMETER_MEMBER_ID);

        if (null === $memberId) {
            return false;
        }

        $companyId = $request->get(self::PARAMETER_COMPANY_ID);

        if ($companyId !== null) {
            $company = $this->companyService->getCompany((int)$companyId);
        } else {
            $memberAssignments = $this->memberService->getMemberAssignmentsByMemberId((int)$memberId);
            $memberAssignment = reset($memberAssignments);

            if (false === $memberAssignment) {
                throw new InvalidArgumentException(self::PARAMETER_MEMBER_ID, 'Member does not belong to any company');
            }

            $company = $this->companyService->getCompany($memberAssignment->getCompanyId());
        }

        $member = $this->memberService->getMember((int)$memberId, $company);

        $request->attributes->set($configuration->getName(), $member);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return Member::class === $configuration->getClass()
            && self::PARAMETER_MEMBER === $configuration->getName();
    }
}
