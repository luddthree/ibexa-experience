<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber;

use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\MemberCreateView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class MemberCreateViewSubscriber extends AbstractViewSubscriber
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
        return $view instanceof MemberCreateView;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\MemberCreateView $view
     */
    protected function configureView(View $view): void
    {
        $view->addParameters([
            'form' => $view->getMemberForm()->createView(),
            'members_group' => $view->getMembersGroup(),
            'parent_location' => $view->getMembersGroup()->contentInfo->getMainLocation(),
            'content_type' => $view->getMemberForm()->getData()->contentType,
            'language' => $view->getLanguage(),
            'location' => null,
            'close_href' => $this->urlGenerator->generate('ibexa.corporate_account.company.details', [
                'companyId' => $view->getCompany()->getId(),
                '_fragment' => 'ibexa-tab-members',
            ]),
            'company' => $view->getCompany(),
        ]);
    }
}
