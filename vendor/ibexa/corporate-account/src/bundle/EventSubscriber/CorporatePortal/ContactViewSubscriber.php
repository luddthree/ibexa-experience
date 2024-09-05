<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber\CorporatePortal;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\CorporatePortal\ContactView;

final class ContactViewSubscriber extends AbstractViewSubscriber
{
    private MemberService $memberService;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        MemberService $memberService,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($siteAccessService, $configResolver);

        $this->memberService = $memberService;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\CompanyDetailsView $view
     */
    protected function configureView(View $view): void
    {
        $company = $view->getCompany();

        $view->addParameters([
            'contact' => $this->memberService->getCompanyContact($company),
            'billing_address' => $company->getBillingAddress(),
        ]);
    }

    protected function supports(View $view): bool
    {
        return $view instanceof ContactView;
    }
}
