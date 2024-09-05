<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\CorporatePortal;

use Ibexa\Bundle\CorporateAccount\Controller\Controller;
use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\User\Invitation\InvitationService;
use Ibexa\Contracts\User\Invitation\Query\InvitationFilter;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\CorporateAccount\Configuration\CorporateAccount;
use Ibexa\CorporateAccount\Form\MemberFormFactory;
use Ibexa\CorporateAccount\Pagerfanta\Adapter\MemberListAdapter;
use Ibexa\CorporateAccount\View\CorporatePortal\MembersView;
use Ibexa\CorporateAccount\View\MemberCreateView;
use Ibexa\CorporateAccount\View\MemberEditView;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class MembersController extends Controller
{
    private ConfigResolverInterface $configResolver;

    private InvitationService $invitationService;

    private MemberFormFactory $formFactory;

    private UserService $userService;

    private LanguageService $languageService;

    private ActionDispatcherInterface $actionDispatcher;

    private UrlGeneratorInterface $urlGenerator;

    private MemberResolver $memberResolver;

    private PermissionResolver $permissionResolver;

    private MemberService $memberService;

    public function __construct(
        CorporateAccount $corporateAccount,
        MemberResolver $memberResolver,
        MemberService $memberService,
        ConfigResolverInterface $configResolver,
        InvitationService $invitationService,
        MemberFormFactory $formFactory,
        UserService $userService,
        LanguageService $languageService,
        ActionDispatcherInterface $actionDispatcher,
        UrlGeneratorInterface $urlGenerator,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($corporateAccount);

        $this->memberResolver = $memberResolver;
        $this->memberService = $memberService;
        $this->configResolver = $configResolver;
        $this->invitationService = $invitationService;
        $this->formFactory = $formFactory;
        $this->userService = $userService;
        $this->languageService = $languageService;
        $this->actionDispatcher = $actionDispatcher;
        $this->urlGenerator = $urlGenerator;
        $this->permissionResolver = $permissionResolver;
    }

    public function showAction(Request $request): BaseView
    {
        $company = $this->memberResolver->getCurrentMember()->getCompany();

        $searchForm = $this->formFactory->getMembersSearchForm();
        $invitationForm = $this->formFactory->getMembersInvitationForm($company);
        $changeRoleForm = $this->formFactory->getChangeRoleForm();

        $criterions = [];
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $criterions[] = new Criterion\FullText($searchForm->getData()['query']);
        }

        $members = new Pagerfanta(
            new MemberListAdapter(
                $this->memberService,
                $company,
                empty($criterions) ? new Criterion\MatchAll() : new Criterion\LogicalAnd($criterions)
            )
        );

        $members->setMaxPerPage($this->configResolver->getParameter('corporate_account.pagination.members_limit'));
        $members->setCurrentPage($request->query->getInt('page', 1));

        $companyMembersUserGroup = $this->userService->loadUserGroup($company->getMembersId());
        $invitations = [];

        if ($this->permissionResolver->canUser('user', 'invite', $companyMembersUserGroup)) {
            $invitations = $this->invitationService->findInvitations(
                new InvitationFilter(
                    null,
                    $companyMembersUserGroup
                )
            );
        }

        return new MembersView(
            '@ibexadesign/customer_portal/members/members.html.twig',
            $company,
            $members,
            $invitations,
            $searchForm,
            $changeRoleForm,
            $invitationForm,
            $this->formFactory->getInvitationResendForm($company),
            $this->formFactory->getInvitationReinviteForm($company)
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function createAction(Request $request)
    {
        $company = $this->memberResolver->getCurrentMember()->getCompany();

        $languages = $this->configResolver->getParameter('languages');
        $language = $this->languageService->loadLanguage(reset($languages));

        $membersId = $company->getContent()->getFieldValue('members')->destinationContentId;
        $membersGroup = $this->userService->loadUserGroup($membersId);

        /** @var \Symfony\Component\Form\Form $createForm */
        $createForm = $this->formFactory->getCreateForm(
            $membersGroup,
            $language->languageCode
        );

        $createForm->handleRequest($request);
        if ($createForm->isSubmitted() && $createForm->isValid() && null !== $createForm->getClickedButton()) {
            $this->actionDispatcher->dispatchFormAction(
                $createForm,
                $createForm->getData(),
                $createForm->getClickedButton()->getName(),
                ['company' => $company]
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.members')
            );
        }

        return new MemberCreateView(
            '@ibexadesign/corporate_account/member/create/create_member.html.twig',
            $createForm,
            $membersGroup,
            $language,
            $company
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function editAction(Request $request, Member $member)
    {
        $company = $member->getCompany();

        /** @var \Symfony\Component\Form\Form $memberEditForm */
        $memberEditForm = $this->formFactory->getEditForm($member);

        $memberEditForm->handleRequest($request);
        if ($memberEditForm->isSubmitted()
            && $memberEditForm->isValid()
            && null !== $memberEditForm->getClickedButton()
        ) {
            $this->actionDispatcher->dispatchFormAction(
                $memberEditForm,
                $memberEditForm->getData(),
                $memberEditForm->getClickedButton()->getName(),
                ['company' => $company]
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.members')
            );
        }

        return new MemberEditView(
            '@ibexadesign/corporate_account/member/edit/edit_member.html.twig',
            $member,
            $company,
            $memberEditForm
        );
    }

    /**
     * @todo: This must be handled as POST after redesign is done.
     */
    public function activateAction(Member $member): Response
    {
        $this->setMemberStatus($member, true);

        return new RedirectResponse(
            $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.members')
        );
    }

    public function deactivateAction(Member $member): Response
    {
        $this->setMemberStatus($member, false);

        return new RedirectResponse(
            $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.members')
        );
    }

    private function setMemberStatus(Member $member, bool $status): void
    {
        $updateStruct = $this->userService->newUserUpdateStruct();
        $updateStruct->enabled = $status;

        $this->userService->updateUser($member->getUser(), $updateStruct);
    }

    public function changeRoleAction(Request $request): Response
    {
        $changeRoleForm = $this->formFactory->getChangeRoleForm();

        $changeRoleForm->handleRequest($request);

        if ($changeRoleForm->isSubmitted() && $changeRoleForm->isValid() && null !== $changeRoleForm->getClickedButton()) {
            $this->actionDispatcher->dispatchFormAction(
                $changeRoleForm,
                $changeRoleForm->getData(),
                'change_corporate_role',
                ['company' => $changeRoleForm->getData()->getMember()->getCompany()]
            );
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.members')
        );
    }
}
