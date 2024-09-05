<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\CorporatePortal;

use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\CorporateAccount\Form\MemberFormFactory;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Annotation\Ignore;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class InvitationController extends Controller
{
    private MemberFormFactory $formFactory;

    private ActionDispatcherInterface $actionDispatcher;

    private MemberResolver $memberResolver;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        MemberResolver $memberResolver,
        MemberFormFactory $formFactory,
        ActionDispatcherInterface $actionDispatcher,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->formFactory = $formFactory;
        $this->actionDispatcher = $actionDispatcher;
        $this->memberResolver = $memberResolver;
        $this->notificationHandler = $notificationHandler;
    }

    public function sendInvitationsAction(Request $request): Response
    {
        $company = $this->memberResolver->getCurrentMember()->getCompany();
        $form = $this->formFactory->getMembersInvitationForm(
            $company
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->actionDispatcher->dispatchFormAction(
                $form,
                $form->getData(),
                $form->getClickedButton()->getName(),
                ['company' => $company]
            );

            $this->notificationHandler->success(
                /** @Desc("Invitations sent") */
                'ibexa.corporate_accounts.invitations.success',
                [],
                'ibexa_corporate_account'
            );
        }

        foreach ($form->getErrors(true, true) as $formError) {
            $this->notificationHandler->warning(/** @Ignore */
                $formError->getMessage()
            );
        }

        return $this->redirectToRoute('ibexa.corporate_account.customer_portal.members', [
            '_fragment' => 'ibexa-tab-invitation',
        ]);
    }

    public function resendAction(Request $request): Response
    {
        $company = $this->memberResolver->getCurrentMember()->getCompany();
        $form = $this->formFactory->getInvitationResendForm($company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Ibexa\Contracts\User\Invitation\Invitation $invitation */
            $invitation = $form->getData();
            $this->actionDispatcher->dispatchFormAction(
                $form,
                $invitation,
                'resend',
                ['company' => $company]
            );

            $this->notificationHandler->success(
                /** @Desc("Invitation to '%email%' resend.") */
                'user_invitation.send.resend',
                ['%email%' => $invitation->getEmail()],
                'ibexa_user_invitation'
            );
        }

        return $this->redirectToRoute('ibexa.corporate_account.customer_portal.members', [
            '_fragment' => 'ibexa-tab-invitation',
        ]);
    }

    public function reinviteAction(Request $request): Response
    {
        $company = $this->memberResolver->getCurrentMember()->getCompany();
        $form = $this->formFactory->getInvitationReinviteForm($company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Ibexa\Contracts\User\Invitation\Invitation $invitation */
            $invitation = $form->getData();
            $this->actionDispatcher->dispatchFormAction(
                $form,
                $invitation,
                'reinvite',
                ['company' => $company]
            );

            $this->notificationHandler->success(
                /** @Desc("Reinvited '%email%'.") */
                'user_invitation.send.reinvite',
                ['%email%' => $invitation->getEmail()],
                'ibexa_user_invitation'
            );
        }

        return $this->redirectToRoute('ibexa.corporate_account.customer_portal.members', [
            '_fragment' => 'ibexa-tab-invitation',
        ]);
    }
}
