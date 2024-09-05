<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber;

use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\CompanyCreateView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CompanyCreateViewSubscriber extends AbstractViewSubscriber
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
        return $view instanceof CompanyCreateView;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\CompanyCreateView $view
     */
    protected function configureView(View $view): void
    {
        $view->addParameters([
            'form' => $view->getCompanyForm()->createView(),
            'parent_location' => $view->getParentLocation(),
            'content_type' => $view->getCompanyForm()->getData()->contentType,
            'language' => $view->getLanguage(),
            'location' => null,
            'close_href' => $this->urlGenerator->generate('ibexa.corporate_account.company.list'),
        ]);
    }
}
