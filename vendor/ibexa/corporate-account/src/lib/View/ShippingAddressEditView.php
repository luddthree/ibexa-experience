<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

class ShippingAddressEditView extends BaseView
{
    private ShippingAddress $shippingAddress;

    private FormInterface $shippingAddressForm;

    private Company $company;

    public function __construct(
        string $templateIdentifier,
        ShippingAddress $shippingAddress,
        Company $company,
        FormInterface $shippingAddressForm
    ) {
        parent::__construct($templateIdentifier);

        $this->shippingAddress = $shippingAddress;
        $this->shippingAddressForm = $shippingAddressForm;
        $this->company = $company;
    }

    /**
     * @return array{
     *     shipping_address: \Ibexa\Contracts\CorporateAccount\Values\ShippingAddress,
     *     shipping_address_form: \Symfony\Component\Form\FormView
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'shipping_address' => $this->shippingAddress,
            'shipping_address_form' => $this->shippingAddressForm->createView(),
        ];
    }

    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(ShippingAddress $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function getShippingAddressForm(): FormInterface
    {
        return $this->shippingAddressForm;
    }

    public function setShippingAddressForm(FormInterface $shippingAddressForm): void
    {
        $this->shippingAddressForm = $shippingAddressForm;
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
