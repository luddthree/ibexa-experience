<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\CorporateAccount\Form\MemberFormFactory;
use Ibexa\CorporateAccount\View\MemberEditView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MemberEditController extends Controller
{
    private MemberFormFactory $formFactory;

    private ActionDispatcherInterface $actionDispatcher;

    private UserService $userService;

    public function __construct(
        MemberFormFactory $formFactory,
        ActionDispatcherInterface $actionDispatcher,
        UserService $userService
    ) {
        $this->formFactory = $formFactory;
        $this->actionDispatcher = $actionDispatcher;
        $this->userService = $userService;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function editAction(
        Request $request,
        Company $company,
        Member $member
    ) {
        /** @var \Symfony\Component\Form\Form $memberEditForm */
        $memberEditForm = $this->formFactory->getEditForm($member);

        $memberEditForm->handleRequest($request);
        if ($memberEditForm->isSubmitted() && $memberEditForm->isValid() && null !== $memberEditForm->getClickedButton()) {
            $this->actionDispatcher->dispatchFormAction(
                $memberEditForm,
                $memberEditForm->getData(),
                $memberEditForm->getClickedButton()->getName(),
                ['company' => $company]
            );

            $response = $this->actionDispatcher->getResponse();
            if ($response !== null) {
                return $response;
            }

            return $this->redirectToRoute('ibexa.corporate_account.company.details', [
                'companyId' => $company->getId(),
                '_fragment' => 'ibexa-tab-members',
            ]);
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
    public function activateAction(Company $company, Member $member): Response
    {
        $this->setMemberStatus($member->getUser(), true);

        return $this->redirectToRoute('ibexa.corporate_account.company.details', [
            'companyId' => $company->getId(),
            '_fragment' => 'ibexa-tab-members',
        ]);
    }

    public function deactivateAction(Company $company, Member $member): Response
    {
        $this->setMemberStatus($member->getUser(), false);

        return $this->redirectToRoute('ibexa.corporate_account.company.details', [
            'companyId' => $company->getId(),
            '_fragment' => 'ibexa-tab-members',
        ]);
    }

    private function setMemberStatus(User $user, bool $status): void
    {
        $updateStruct = $this->userService->newUserUpdateStruct();
        $updateStruct->enabled = $status;

        $this->userService->updateUser($user, $updateStruct);
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

            $response = $this->actionDispatcher->getResponse();
            if ($response !== null) {
                return $response;
            }

            return $this->redirectToRoute('ibexa.corporate_account.company.details', [
                'companyId' => $changeRoleForm->getData()->getMember()->getCompany()->getId(),
                '_fragment' => 'ibexa-tab-members',
            ]);
        }

        return $this->redirectToRoute('ibexa.corporate_account.company.list');
    }
}
