<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber;

use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\ShippingAddressEditView;
use RuntimeException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ShippingAddressEditViewSubscriber extends AbstractViewSubscriber
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
        return $view instanceof ShippingAddressEditView;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\ShippingAddressEditView $view
     */
    protected function configureView(View $view): void
    {
        $shippingAddress = $view->getShippingAddress();
        $location = $shippingAddress->getContent()->contentInfo->getMainLocation();
        if ($location === null) {
            throw new RuntimeException('Missing Location for edited address');
        }
        $language = $shippingAddress->getContent()->contentInfo->getMainLanguage();
        $parentLocation = $location->getParentLocation();
        $contentType = $shippingAddress->getContent()->getContentType();
        $company = $view->getCompany();

        $view->addParameters([
            'form' => $view->getShippingAddressForm()->createView(),
            'language' => $language,
            'location' => $location,
            'parent_location' => $parentLocation,
            'content' => $shippingAddress->getContent(),
            'shipping_address' => $shippingAddress,
            'is_published' => true,
            'content_type' => $contentType,
            'close_href' => $this->urlGenerator->generate('ibexa.corporate_account.company.details', [
                'companyId' => $company->getId(),
                '_fragment' => 'ibexa-tab-address_book',
            ]),
        ]);
    }
}
