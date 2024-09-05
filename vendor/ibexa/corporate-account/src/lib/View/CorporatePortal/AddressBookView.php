<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View\CorporatePortal;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

class AddressBookView extends BaseView
{
    private string $string;

    private Company $company;

    /** @var iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Content> */
    private iterable $addresses;

    private FormInterface $formDefaultShippingAddressUpdate;

    private FormInterface $formAddressBookItemDelete;

    /**
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $addresses
     */
    public function __construct(
        string $templateIdentifier,
        Company $company,
        iterable $addresses,
        FormInterface $formDefaultShippingAddressUpdate,
        FormInterface $formAddressBookItemDelete
    ) {
        parent::__construct($templateIdentifier);
        $this->company = $company;
        $this->addresses = $addresses;
        $this->formDefaultShippingAddressUpdate = $formDefaultShippingAddressUpdate;
        $this->formAddressBookItemDelete = $formAddressBookItemDelete;
    }

    /**
     * @return array{
     *     company: \Ibexa\Contracts\CorporateAccount\Values\Company,
     *     address_list: iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Content>,
     *     default_shipping_form: \Symfony\Component\Form\FormView,
     *     delete_address_form: \Symfony\Component\Form\FormView
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'company' => $this->company,
            'address_list' => $this->addresses,
            'default_shipping_form' => $this->formDefaultShippingAddressUpdate->createView(),
            'delete_address_form' => $this->formAddressBookItemDelete->createView(),
        ];
    }

    public function getString(): string
    {
        return $this->string;
    }

    public function setString(string $string): void
    {
        $this->string = $string;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    /** @return iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Content> */
    public function getAddresses(): iterable
    {
        return $this->addresses;
    }

    /** @param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $addresses */
    public function setAddresses(iterable $addresses): void
    {
        $this->addresses = $addresses;
    }

    public function getFormDefaultShippingAddressUpdate(): FormInterface
    {
        return $this->formDefaultShippingAddressUpdate;
    }

    public function setFormDefaultShippingAddressUpdate(FormInterface $formDefaultShippingAddressUpdate): void
    {
        $this->formDefaultShippingAddressUpdate = $formDefaultShippingAddressUpdate;
    }

    public function getFormAddressBookItemDelete(): FormInterface
    {
        return $this->formAddressBookItemDelete;
    }

    public function setFormAddressBookItemDelete(FormInterface $formAddressBookItemDelete): void
    {
        $this->formAddressBookItemDelete = $formAddressBookItemDelete;
    }
}
