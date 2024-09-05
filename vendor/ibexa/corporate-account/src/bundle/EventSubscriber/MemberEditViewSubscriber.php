<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber;

use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\MemberEditView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class MemberEditViewSubscriber extends AbstractViewSubscriber
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
        return $view instanceof MemberEditView;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\MemberEditView $view
     */
    protected function configureView(View $view): void
    {
        $memberUser = $view->getMember()->getUser();
        $location = $memberUser->getVersionInfo()->getContentInfo()->getMainLocation();
        if ($location === null) {
            throw new \RuntimeException('Missing Location for edited member');
        }
        $language = $memberUser->getVersionInfo()->getContentInfo()->getMainLanguage();
        $parentLocation = $location->getParentLocation();
        $contentType = $memberUser->getContentType();
        $company = $view->getCompany();

        $view->addParameters([
            'form' => $view->getMemberForm()->createView(),
            'language' => $language,
            'location' => $location,
            'parent_location' => $parentLocation,
            'content' => $memberUser,
            'is_published' => true,
            'content_type' => $contentType,
            'close_href' => $this->urlGenerator->generate('ibexa.corporate_account.company.details', [
                'companyId' => $company->getId(),
                '_fragment' => 'ibexa-tab-members',
            ]),
        ]);
    }
}
