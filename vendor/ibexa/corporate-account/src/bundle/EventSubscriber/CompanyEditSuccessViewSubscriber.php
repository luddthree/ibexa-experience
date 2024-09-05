<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber;

use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\CompanyEditSuccessView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CompanyEditSuccessViewSubscriber extends AbstractViewSubscriber
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($siteAccessService);

        $this->urlGenerator = $urlGenerator;
    }

    protected function supports(View $view): bool
    {
        return $view instanceof CompanyEditSuccessView;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\CompanyEditSuccessView $view
     */
    protected function configureView(View $view): void
    {
        $view->
        $view->setResponse(
            new RedirectResponse(
                $this->urlGenerator->generate('ibexa.corporate_account.company.details', [
                    'companyId' => $view->getCompany()->id,
                ])
            )
        );
    }
}
