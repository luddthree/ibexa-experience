<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Security;

use Ibexa\Contracts\CorporateAccount\CustomerPortal\CustomerPortalResolver;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\Contracts\CorporateAccount\Service\CustomerPortalService;
use Ibexa\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Ibexa\Core\MVC\Symfony\Security\Authentication\DetermineTargetUrlEvent;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\CorporateAccount\Specification\IsCorporate;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class RedirectToCustomerPortalListener implements EventSubscriberInterface
{
    use TargetPathTrait;

    private SiteAccessServiceInterface $siteAccessService;

    private CustomerPortalResolver $customerPortalResolver;

    private CustomerPortalService $customerPortalService;

    private MemberResolver $memberResolver;

    private RouterInterface $router;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        CustomerPortalResolver $customerPortalResolver,
        CustomerPortalService $customerPortalService,
        MemberResolver $memberResolver,
        RouterInterface $router
    ) {
        $this->siteAccessService = $siteAccessService;
        $this->customerPortalResolver = $customerPortalResolver;
        $this->memberResolver = $memberResolver;
        $this->router = $router;
        $this->customerPortalService = $customerPortalService;
    }

    public static function getSubscribedEvents(): array
    {
        return [DetermineTargetUrlEvent::class => 'determineTargetUrl'];
    }

    public function determineTargetUrl(DetermineTargetUrlEvent $event): void
    {
        $currentSiteAccess = $this->siteAccessService->getCurrent();

        if ($currentSiteAccess === null || !(new IsCorporate($this->siteAccessService))->isSatisfiedBy($currentSiteAccess)) {
            return;
        }

        $location = $this->customerPortalResolver->resolveCustomerPortalLocation(
            $this->memberResolver->getCurrentMember(),
            $currentSiteAccess->name
        );

        if ($location === null) {
            $this->setTargetPathToCustomerCenter($event);

            return;
        }

        $mainPage = $this->customerPortalService->getMainPage($location);
        $mainPageLocation = $mainPage->getVersionInfo()->getContentInfo()->getMainLocation();

        if ($mainPageLocation === null) {
            $this->setTargetPathToCustomerCenter($event);

            return;
        }

        $route = $this->router->generate(
            UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            [
                'locationId' => $mainPageLocation->id,
                'contentId' => $mainPage->id,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->saveTargetPath(
            $event->getRequest()->getSession(),
            $event->getFirewallName(),
            $route
        );
    }

    private function setTargetPathToCustomerCenter(DetermineTargetUrlEvent $event): void
    {
        $route = $this->router->generate(
            'ibexa.corporate_account.customer_portal.customer_center',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->saveTargetPath(
            $event->getRequest()->getSession(),
            $event->getFirewallName(),
            $route
        );
    }
}
