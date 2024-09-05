<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\CorporatePortal;

use Ibexa\Bundle\CorporateAccount\Controller\Controller;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\CorporateAccount\CustomerPortal\CustomerPortalResolver;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\Contracts\CorporateAccount\Service\CustomerPortalService;
use Ibexa\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\CorporateAccount\Configuration\CorporateAccount;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

final class CustomerPortalEntryController extends Controller
{
    private CustomerPortalService $customerPortalService;

    private RouterInterface $router;

    private CustomerPortalResolver $customerPortalResolver;

    private MemberResolver $memberResolver;

    private SiteAccessServiceInterface $siteAccessService;

    private LocationService $locationService;

    public function __construct(
        CorporateAccount $corporateAccount,
        LocationService $locationService,
        CustomerPortalService $customerPortalService,
        RouterInterface $router,
        MemberResolver $memberResolver,
        CustomerPortalResolver $customerPortalResolver,
        SiteAccessServiceInterface $siteAccessService
    ) {
        parent::__construct($corporateAccount);

        $this->customerPortalService = $customerPortalService;
        $this->router = $router;
        $this->memberResolver = $memberResolver;
        $this->customerPortalResolver = $customerPortalResolver;
        $this->siteAccessService = $siteAccessService;
        $this->locationService = $locationService;
    }

    public function redirectToMainPageAction(int $locationId): RedirectResponse
    {
        $mainPage = $this->customerPortalService->getMainPage(
            $this->locationService->loadLocation($locationId)
        );

        $mainPageLocation = $mainPage->getVersionInfo()->getContentInfo()->getMainLocation();

        if ($mainPageLocation === null) {
            return $this->redirectToCustomerCenter();
        }

        $route = $this->router->generate(
            UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            [
                'locationId' => $mainPageLocation->id,
                'contentId' => $mainPage->id,
            ]
        );

        return new RedirectResponse($route);
    }

    public function redirectToPortalAction(): RedirectResponse
    {
        $currentSiteAccess = $this->siteAccessService->getCurrent();

        if ($currentSiteAccess === null) {
            return $this->redirectToCustomerCenter();
        }

        $location = $this->customerPortalResolver->resolveCustomerPortalLocation(
            $this->memberResolver->getCurrentMember(),
            $currentSiteAccess->name
        );

        if ($location === null) {
            return $this->redirectToCustomerCenter();
        }

        return $this->redirectToLocation($location);
    }

    private function redirectToCustomerCenter(): RedirectResponse
    {
        return new RedirectResponse(
            $this->router->generate(
                'ibexa.corporate_account.customer_portal.customer_center'
            )
        );
    }
}
