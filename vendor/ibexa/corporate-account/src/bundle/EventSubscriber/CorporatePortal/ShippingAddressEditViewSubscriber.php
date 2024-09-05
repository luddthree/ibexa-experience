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
use Ibexa\CorporateAccount\View\ShippingAddressEditView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ShippingAddressEditViewSubscriber extends AbstractViewSubscriber
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
        return $view instanceof ShippingAddressEditView;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\ShippingAddressEditView $view
     */
    protected function configureView(View $view): void
    {
        $shippingAddress = $view->getShippingAddress()->getContent();
        $location = $shippingAddress->contentInfo->getMainLocation();
        if ($location === null) {
            throw new \RuntimeException('Missing Location for edited address');
        }
        $language = $shippingAddress->contentInfo->getMainLanguage();
        $parentLocation = $location->getParentLocation();
        $contentType = $shippingAddress->getContentType();

        $view->addParameters([
            'form' => $view->getShippingAddressForm()->createView(),
            'language' => $language,
            'location' => $location,
            'parent_location' => $parentLocation,
            'content' => $shippingAddress,
            'is_published' => true,
            'content_type' => $contentType,
            'close_href' => $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.address_book'),
        ]);
    }
}
