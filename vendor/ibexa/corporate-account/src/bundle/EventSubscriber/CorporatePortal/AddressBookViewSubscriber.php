<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber\CorporatePortal;

use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\CorporatePortal\AddressBookView;

final class AddressBookViewSubscriber extends AbstractViewSubscriber
{
    /**
     * @param \Ibexa\CorporateAccount\View\CompanyDetailsView $view
     */
    protected function configureView(View $view): void
    {
        $company = $view->getCompany();

        $view->addParameters([
            'billing_address' => $company->getBillingAddress(),
        ]);
    }

    protected function supports(View $view): bool
    {
        return $view instanceof AddressBookView;
    }
}
