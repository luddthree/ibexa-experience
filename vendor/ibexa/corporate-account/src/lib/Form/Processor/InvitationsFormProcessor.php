<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Processor;

use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;
use Ibexa\Contracts\CorporateAccount\Exception\CorporateSiteAccessConfigurationException;
use Ibexa\Contracts\User\Invitation\Exception\InvitationAlreadyExistsException;
use Ibexa\Contracts\User\Invitation\Invitation;
use Ibexa\Contracts\User\Invitation\InvitationCreateStruct;
use Ibexa\Contracts\User\Invitation\InvitationSender;
use Ibexa\Contracts\User\Invitation\InvitationService;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\CorporateAccount\Event\DispatcherEvents;
use Ibexa\CorporateAccount\Form\Data\Invitation\InviteMembersData;
use Ibexa\CorporateAccount\Form\Data\Invitation\InviteMembersWithSiteAccessData;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvitationsFormProcessor implements EventSubscriberInterface
{
    private UserService $userService;

    private InvitationService $invitationService;

    private InvitationSender $sender;

    private SiteAccessServiceInterface $siteAccessService;

    /** @var array<string, array<string>> */
    private array $siteAccessGroups;

    /** @param array<string, array<string>> $siteAccessGroups */
    public function __construct(
        UserService $userService,
        InvitationService $invitationService,
        InvitationSender $sender,
        SiteAccessServiceInterface $siteAccessService,
        array $siteAccessGroups
    ) {
        $this->userService = $userService;
        $this->invitationService = $invitationService;
        $this->sender = $sender;
        $this->siteAccessService = $siteAccessService;
        $this->siteAccessGroups = $siteAccessGroups;
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            DispatcherEvents::COMPANY_INVITATIONS_SEND => ['processCreateAndSend', 10],
            DispatcherEvents::COMPANY_INVITATIONS_RESEND => ['processResend', 10],
            DispatcherEvents::COMPANY_INVITATIONS_REINVITE => [
                ['processRefresh', 20],
                ['processResend', 10],
            ],
        ];
    }

    public function processCreateAndSend(FormActionEvent $event): void
    {
        $data = $event->getData();

        if (!$data instanceof InviteMembersData && !$data instanceof InviteMembersWithSiteAccessData) {
            return;
        }
        /** @var \Ibexa\Contracts\CorporateAccount\Values\Company $company */
        $company = $event->getOption('company');

        $siteAccessIdentifier = $data instanceof InviteMembersWithSiteAccessData
            ? $data->getSiteAccess()->name
            : $this->getCurrentSiteAccessIdentifier();

        $membersGroup = $this->userService->loadUserGroup(
            $company->getMembersId()
        );
        foreach ($data->getInvitations() as $simpleInvitation) {
            if ($simpleInvitation->getEmail() === null || $simpleInvitation->getRole() === null) {
                continue;
            }
            $struct = new InvitationCreateStruct(
                $simpleInvitation->getEmail(),
                $siteAccessIdentifier,
                $membersGroup,
                $simpleInvitation->getRole(),
                new SubtreeLimitation(
                    [
                        'limitationValues' => [
                            $company->getContent()->getVersionInfo()->getContentInfo()->getMainLocation()->pathString,
                        ],
                    ]
                )
            );

            try {
                $invitation = $this->invitationService->createInvitation($struct);
                $this->sender->sendInvitation($invitation);
            } catch (InvitationAlreadyExistsException $exception) {
                $existingInvitation = $this->invitationService->getInvitationByEmail($simpleInvitation->getEmail());
                $this->sender->sendInvitation(
                    $this->invitationService->refreshInvitation(
                        $existingInvitation
                    )
                );
            }
        }
    }

    public function processRefresh(FormActionEvent $event): void
    {
        $data = $event->getData();

        if (!$data instanceof Invitation) {
            return;
        }
        $refreshed = $this->invitationService->refreshInvitation($data);
        $event->setData($refreshed);
    }

    public function processResend(FormActionEvent $event): void
    {
        $data = $event->getData();

        if (!$data instanceof Invitation) {
            return;
        }
        $this->sender->sendInvitation($data);
    }

    private function getCurrentSiteAccessIdentifier(): string
    {
        $currentSiteAccess = $this->siteAccessService->getCurrent();

        if ($currentSiteAccess === null) {
            $defaultSiteAccess = reset($this->siteAccessGroups['corporate_group']);
            if (!$defaultSiteAccess) {
                throw new CorporateSiteAccessConfigurationException(
                    'There is no site access configured for `corporate_group` group'
                );
            }

            return $defaultSiteAccess;
        }

        $siteAccessIdentifier = $currentSiteAccess->name;
        if (!in_array($siteAccessIdentifier, $this->siteAccessGroups['corporate_group'], true)) {
            throw new CorporateSiteAccessConfigurationException(
                'Current SiteAccess is not a part of `corporate_group`'
            );
        }

        return $siteAccessIdentifier;
    }
}
