<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Processor;

use Ibexa\AdminUi\Specification\SiteAccess\IsAdmin;
use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\CorporateAccount\Event\DispatcherEvents;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class MemberNotificationFormProcessor extends FormProcessor
{
    private TranslatableNotificationHandlerInterface $notificationHandler;

    private RequestStack $requestStack;

    /** @var string[][] */
    private array $siteAccessGroups;

    /**
     * @param string[][] $siteAccessGroups
     */
    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        RequestStack $requestStack,
        array $siteAccessGroups
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->requestStack = $requestStack;
        $this->siteAccessGroups = $siteAccessGroups;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DispatcherEvents::MEMBER_CREATE => ['addCreateSuccessNotification', 5],
            DispatcherEvents::MEMBER_UPDATE => ['addUpdateSuccessNotification', 5],
        ];
    }

    public function addCreateSuccessNotification(FormActionEvent $event): void
    {
        if (!$this->isAdminSiteAccess($this->requestStack->getCurrentRequest())) {
            return;
        }

        $this->notificationHandler->success(
            /** @Desc("Member created.") */
            'member.create.success',
            [],
            'ibexa_corporate_account'
        );
    }

    public function addUpdateSuccessNotification(FormActionEvent $event): void
    {
        if (!$this->isAdminSiteAccess($this->requestStack->getCurrentRequest())) {
            return;
        }

        /** @var \Ibexa\CorporateAccount\Form\Data\Member\MemberUpdateData $data */
        $data = $event->getData();

        $this->notificationHandler->success(
            /** @Desc("Member '%name%' updated.") */
            'member.edit.success',
            ['%name%' => $data->getMember()->getName()],
            'ibexa_corporate_account'
        );
    }

    protected function isAdminSiteAccess(?Request $request): bool
    {
        if ($request === null) {
            return false;
        }

        return (new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($request->attributes->get('siteaccess'));
    }
}
