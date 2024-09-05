<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber\CorporatePortal;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\CorporatePortal\CustomerCenterView;

final class CustomerCenterViewSubscriber extends AbstractViewSubscriber
{
    private CompanyService $companyService;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        CompanyService $companyService,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($siteAccessService, $configResolver);

        $this->companyService = $companyService;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\CorporatePortal\CustomerCenterView $view
     */
    protected function configureView(View $view): void
    {
        $company = $view->getCompany();

        $view->addParameters([
            'sales_rep' => $this->companyService->getCompanySalesRepresentative($company),
        ]);
    }

    protected function supports(View $view): bool
    {
        return $view instanceof CustomerCenterView;
    }
}
