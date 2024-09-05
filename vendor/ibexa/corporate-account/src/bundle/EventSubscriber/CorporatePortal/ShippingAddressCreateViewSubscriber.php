<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber\CorporatePortal;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\ShippingAddressCreateView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ShippingAddressCreateViewSubscriber extends AbstractViewSubscriber
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        UrlGeneratorInterface $urlGenerator,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($siteAccessService, $configResolver);

        $this->urlGenerator = $urlGenerator;
    }

    protected function supports(View $view): bool
    {
        return $view instanceof ShippingAddressCreateView;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\ShippingAddressCreateView $view
     */
    protected function configureView(View $view): void
    {
        $view->addParameters([
            'form' => $view->getShippingAddressForm()->createView(),
            'parent_location' => $view->getParentLocation(),
            'content_type' => $view->getShippingAddressForm()->getData()->contentType,
            'language' => $view->getLanguage(),
            'location' => null,
            'company' => $view->getCompany(),
            'close_href' => $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.address_book'),
        ]);
    }
}
