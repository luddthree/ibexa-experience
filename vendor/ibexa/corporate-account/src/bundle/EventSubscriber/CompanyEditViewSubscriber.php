<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber;

use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\CompanyEditView;

final class CompanyEditViewSubscriber extends AbstractViewSubscriber
{
    public function __construct(
        SiteAccessServiceInterface $siteAccessService
    ) {
        parent::__construct($siteAccessService);
    }

    protected function supports(View $view): bool
    {
        return $view instanceof CompanyEditView;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\CompanyEditView $view
     */
    protected function configureView(View $view): void
    {
        $company = $view->getCompany();

        $companyContentInfo = $company->getContent()->getVersionInfo()->getContentInfo();
        $location = $companyContentInfo->getMainLocation();
        if ($location === null) {
            throw new \RuntimeException('Missing Location for edited company');
        }
        $language = $companyContentInfo->getMainLanguage();
        $parentLocation = $location->getParentLocation();
        $contentType = $company->getContent()->getContentType();

        $view->addParameters([
            'form' => $view->getCompanyForm()->createView(),
            'language' => $language,
            'location' => $location,
            'parent_location' => $parentLocation,
            'content' => $company->getContent(),
            'is_published' => true,
            'content_type' => $contentType,
        ]);
    }
}
