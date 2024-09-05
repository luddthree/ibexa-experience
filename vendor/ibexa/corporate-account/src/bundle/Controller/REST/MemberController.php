<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\REST;

use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Company as APICompany;
use Ibexa\Contracts\CorporateAccount\Values\Member as APIMember;
use Ibexa\CorporateAccount\REST\QueryBuilder\ContentQueryBuilderInterface;
use Ibexa\CorporateAccount\REST\Value\Company;
use Ibexa\CorporateAccount\REST\Value\Member;
use Ibexa\CorporateAccount\REST\Value\MemberCreateStruct;
use Ibexa\CorporateAccount\REST\Value\MemberList;
use Ibexa\CorporateAccount\REST\Value\MemberUpdateStruct;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Server\Values\NoContent;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class MemberController extends RestController
{
    private MemberService $memberService;

    private ContentQueryBuilderInterface $queryBuilder;

    public function __construct(
        ContentQueryBuilderInterface $queryBuilder,
        MemberService $memberService
    ) {
        $this->memberService = $memberService;
        $this->queryBuilder = $queryBuilder;
    }

    public function getCompanyMembers(Request $request, APICompany $company): MemberList
    {
        $query = $this->queryBuilder->buildQuery(
            $request,
            MemberService::DEFAULT_COMPANY_MEMBERS_LIST_LIMIT
        );

        return new MemberList(
            new Company($company),
            array_map(
                static fn (APIMember $member): Member => new Member($member),
                $this->memberService->getCompanyMembers(
                    $company,
                    $query->filter,
                    $query->sortClauses,
                    $query->limit,
                    $query->offset
                )
            )
        );
    }

    /**
     * @see \Ibexa\Bundle\CorporateAccount\ParamConverter\REST\MemberCreateStructParamConverter
     * @see \Ibexa\CorporateAccount\REST\Input\Parser\MemberCreate
     */
    public function createMember(APICompany $company, MemberCreateStruct $memberCreateStruct): Member
    {
        return new Member(
            $this->memberService->createMember(
                $company,
                $memberCreateStruct->memberCreateStruct,
                $memberCreateStruct->role
            )
        );
    }

    public function getMember(APIMember $member): Member
    {
        return new Member($member);
    }

    public function deleteMember(APIMember $member): NoContent
    {
        $this->memberService->deleteMember($member);

        return new NoContent();
    }

    /**
     * @see \Ibexa\Bundle\CorporateAccount\ParamConverter\REST\MemberUpdateStructParamConverter
     * @see \Ibexa\CorporateAccount\REST\Input\Parser\MemberUpdate
     */
    public function updateMember(APIMember $member, MemberUpdateStruct $memberUpdateStruct): Member
    {
        $updatedMember = $this->memberService->updateMember(
            $member,
            $memberUpdateStruct->memberUpdateStruct
        );

        if (
            $memberUpdateStruct->newRole !== null
            && $updatedMember->getRole()->identifier !== $memberUpdateStruct->newRole->identifier
        ) {
            $this->memberService->setMemberRole($updatedMember, $memberUpdateStruct->newRole);
            // reload member after updating his role
            $updatedMember = $this->memberService->getMember(
                $updatedMember->getId(),
                $member->getCompany()
            );
        }

        return new Member($updatedMember);
    }
}
