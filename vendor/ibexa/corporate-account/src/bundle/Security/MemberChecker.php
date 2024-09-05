<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Security;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\MVC\Symfony\Security\UserInterface as IbexaUserInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Specification\IsCorporate;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class MemberChecker implements UserCheckerInterface
{
    private UserCheckerInterface $checker;

    private MemberService $memberService;

    private CompanyService $companyService;

    private CorporateAccountConfiguration $corporateAccountConfiguration;

    private SiteAccessServiceInterface $siteAccessService;

    private Repository $repository;

    public function __construct(
        UserCheckerInterface $checker,
        MemberService $memberService,
        CompanyService $companyService,
        CorporateAccountConfiguration $corporateAccountConfiguration,
        SiteAccessServiceInterface $siteAccessService,
        Repository $repository
    ) {
        $this->checker = $checker;
        $this->memberService = $memberService;
        $this->corporateAccountConfiguration = $corporateAccountConfiguration;
        $this->siteAccessService = $siteAccessService;
        $this->companyService = $companyService;
        $this->repository = $repository;
    }

    public function checkPreAuth(UserInterface $user): void
    {
        $this->checker->checkPreAuth($user);

        $isCorporateSiteAccess = new IsCorporate($this->siteAccessService);
        $currentSiteAccess = $this->siteAccessService->getCurrent();
        if ($currentSiteAccess === null || !$isCorporateSiteAccess->isSatisfiedBy($currentSiteAccess)) {
            return;
        }

        if (!$user instanceof IbexaUserInterface) {
            return;
        }

        if ($user->getAPIUser()->getContentType()->identifier
            !== $this->corporateAccountConfiguration->getMemberContentTypeIdentifier()
        ) {
            throw new UserIsNotMemberException(
                $user,
                $this->corporateAccountConfiguration->getMemberContentTypeIdentifier()
            );
        }

        $company = $this->getMemberCompany($user);

        if (!$company->isActive()) {
            throw new DeactivatedCompanyException($user);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        $this->checker->checkPostAuth($user);
    }

    private function getMemberCompany(IbexaUserInterface $user): Company
    {
        $assignments = $this->memberService->getMemberAssignmentsByMemberId($user->getAPIUser()->getUserId());

        if (empty($assignments)) {
            throw new MemberNotPartOfCompanyException($user);
        }

        /** @var \Ibexa\Contracts\CorporateAccount\Values\MemberAssignment $firstAssignment */
        $firstAssignment = reset($assignments);

        return $this->repository->sudo(
            fn (Repository $repository): Company => $this->companyService->getCompany($firstAssignment->getCompanyId()),
            $this->repository
        );
    }
}
