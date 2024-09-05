<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber\CorporatePortal;

use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\CorporateAccount\Specification\IsCorporate;
use Ibexa\User\View\ChangePassword\SuccessView;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ChangePasswordSuccessViewSubscriber implements EventSubscriberInterface
{
    private UrlGeneratorInterface $urlGenerator;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private SiteAccessServiceInterface $siteAccessService;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        UrlGeneratorInterface $urlGenerator,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->notificationHandler = $notificationHandler;
        $this->siteAccessService = $siteAccessService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onView', 10],
        ];
    }

    private function supports(ViewEvent $view): bool
    {
        $currentSiteAccess = $this->siteAccessService->getCurrent();
        if (!$currentSiteAccess) {
            return false;
        }

        return $view->getControllerResult() instanceof SuccessView
            && (new IsCorporate($this->siteAccessService))->isSatisfiedBy($currentSiteAccess);
    }

    public function onView(ViewEvent $view): void
    {
        if (!$this->supports($view)) {
            return;
        }

        $this->notificationHandler->success(
            /** @Desc("Your password has been successfully changed.") */
            'ezplatform.change_password.success',
            [],
            'ibexa_change_password'
        );

        $view->setResponse(
            new RedirectResponse(
                $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.my_profile')
            )
        );
    }
}
