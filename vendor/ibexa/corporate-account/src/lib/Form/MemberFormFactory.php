<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\Data\Invitation\InviteMembersData;
use Ibexa\CorporateAccount\Form\Data\Invitation\InviteMembersWithSiteAccessData;
use Ibexa\CorporateAccount\Form\Data\Member\MemberCreateMapper;
use Ibexa\CorporateAccount\Form\Data\Member\MemberUpdateMapper;
use Ibexa\CorporateAccount\Form\Type\Individual\IndividualSearchType;
use Ibexa\CorporateAccount\Form\Type\Invitation\InviteMembersType;
use Ibexa\CorporateAccount\Form\Type\Invitation\InviteMembersWithSiteAccessType;
use Ibexa\CorporateAccount\Form\Type\Member\MemberCreateType;
use Ibexa\CorporateAccount\Form\Type\Member\MemberEditType;
use Ibexa\CorporateAccount\Form\Type\Member\MemberRoleChangeType;
use Ibexa\CorporateAccount\Form\Type\Member\MemberSearchType;
use Ibexa\User\Form\Type\Invitation\InvitationType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MemberFormFactory extends ContentFormFactory
{
    private ContentTypeService $contentTypeService;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        FormFactoryInterface $formFactory,
        CorporateAccountConfiguration $corporateAccount,
        ContentTypeService $contentTypeService,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($formFactory, $corporateAccount);

        $this->contentTypeService = $contentTypeService;
        $this->urlGenerator = $urlGenerator;
    }

    public function getEditForm(Member $member): FormInterface
    {
        $contentInfo = $member->getUser()->getVersionInfo()->getContentInfo();
        $contentType = $member->getContentType();

        $memberUpdateData = (new MemberUpdateMapper())->mapToFormData(
            $member,
            $contentType,
            [
                'languageCode' => $contentInfo->mainLanguageCode,
            ]
        );

        return $this->formFactory->create(MemberEditType::class, $memberUpdateData, [
            'languageCode' => $contentInfo->mainLanguageCode,
            'mainLanguageCode' => $contentInfo->mainLanguageCode,
        ]);
    }

    public function getCreateForm(UserGroup $userGroup, string $languageCode): FormInterface
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $this->corporateAccount->getMemberContentTypeIdentifier()
        );

        $memberCreateData = (new MemberCreateMapper())->mapToFormData(
            $contentType,
            [$userGroup],
            [
                'mainLanguageCode' => $languageCode,
            ]
        );

        return $this->formFactory->create(MemberCreateType::class, $memberCreateData, [
            'languageCode' => $languageCode,
            'mainLanguageCode' => $languageCode,
        ]);
    }

    public function getMembersSearchForm(): FormInterface
    {
        return $this->formFactory->create(MemberSearchType::class, null, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
    }

    public function getIndividualSearchForm(): FormInterface
    {
        return $this->formFactory->create(IndividualSearchType::class, null, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
    }

    public function getChangeRoleForm(): FormInterface
    {
        return $this->formFactory->create(
            MemberRoleChangeType::class
        );
    }

    public function getMembersInvitationForm(Company $company): FormInterface
    {
        return $this->formFactory->create(
            InviteMembersType::class,
            new InviteMembersData(),
            [
                'action' => $this->urlGenerator->generate(
                    'ibexa.corporate_account.company.invite',
                    [
                        'companyId' => $company->getId(),
                    ]
                ),
            ]
        );
    }

    public function getInviteMembersWithSiteAccessForm(Company $company): FormInterface
    {
        return $this->formFactory->create(
            InviteMembersWithSiteAccessType::class,
            new InviteMembersWithSiteAccessData(),
            [
                'action' => $this->urlGenerator->generate(
                    'ibexa.corporate_account.company.invite',
                    [
                        'companyId' => $company->getId(),
                    ]
                ),
            ]
        );
    }

    public function getInvitationResendForm(Company $company): FormInterface
    {
        return $this->formFactory->createNamed(
            'ibexa_user_invitation_resend-form',
            InvitationType::class,
            null,
            [
                'action' => $this->urlGenerator->generate(
                    'ibexa.corporate_account.company.invite.resend',
                    [
                        'companyId' => $company->getId(),
                    ]
                ),
            ]
        );
    }

    public function getInvitationReinviteForm(Company $company): FormInterface
    {
        return $this->formFactory->createNamed(
            'ibexa_user_invitation_reinvite-form',
            InvitationType::class,
            null,
            [
                'action' => $this->urlGenerator->generate(
                    'ibexa.corporate_account.company.invite.reinvite',
                    [
                        'companyId' => $company->getId(),
                    ]
                ),
            ]
        );
    }
}
