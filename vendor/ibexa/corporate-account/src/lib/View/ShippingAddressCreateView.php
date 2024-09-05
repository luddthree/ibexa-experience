<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

class ShippingAddressCreateView extends BaseView
{
    private FormInterface $shippingAddressForm;

    private Location $parentLocation;

    private Language $language;

    private Company $company;

    public function __construct(
        string $templateIdentifier,
        FormInterface $shippingAddressForm,
        Location $parentLocation,
        Language $language,
        Company $company
    ) {
        parent::__construct($templateIdentifier);

        $this->shippingAddressForm = $shippingAddressForm;
        $this->parentLocation = $parentLocation;
        $this->language = $language;
        $this->company = $company;
    }

    /**
     * @return array{
     *     shipping_address_form: \Symfony\Component\Form\FormView
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'shipping_address_form' => $this->shippingAddressForm->createView(),
        ];
    }

    public function getShippingAddressForm(): FormInterface
    {
        return $this->shippingAddressForm;
    }

    public function setShippingAddressForm(FormInterface $shippingAddressForm): void
    {
        $this->shippingAddressForm = $shippingAddressForm;
    }

    public function getParentLocation(): Location
    {
        return $this->parentLocation;
    }

    public function setParentLocation(Location $parentLocation): void
    {
        $this->parentLocation = $parentLocation;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }
}
