<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Values;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\Core\FieldType\Checkbox\Value as CheckboxValue;
use Ibexa\FieldTypeAddress\FieldType\Value as AddressValue;

final class Company extends ValueObject
{
    private Content $content;

    private ?Location $location = null;

    public function __construct(Content $content)
    {
        parent::__construct();

        $this->content = $content;
    }

    public function getName(): ?string
    {
        return $this->content->getName();
    }

    public function getId(): int
    {
        return $this->content->id;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getBillingAddress(): AddressValue
    {
        /** @var \Ibexa\FieldTypeAddress\FieldType\Value $addressValue */
        $addressValue = $this->getContent()->getFieldValue('billing_address');

        return $addressValue;
    }

    public function getContactId(): ?int
    {
        /** @var \Ibexa\Core\FieldType\Relation\Value $contactValue */
        $contactValue = $this->content->getFieldValue('contact');

        return $contactValue->destinationContentId;
    }

    public function getSalesRepresentativeId(): ?int
    {
        /** @var \Ibexa\Core\FieldType\Relation\Value $salesRepresentativeValue */
        $salesRepresentativeValue = $this->content->getFieldValue('sales_rep');

        return $salesRepresentativeValue->destinationContentId;
    }

    public function getDefaultAddressId(): ?int
    {
        /** @var \Ibexa\Core\FieldType\Relation\Value $defaultAddressValue */
        $defaultAddressValue = $this->content->getFieldValue('default_address');

        return $defaultAddressValue->destinationContentId;
    }

    public function getAddressBookId(): ?int
    {
        /** @var \Ibexa\Core\FieldType\Relation\Value $addressBookValue */
        $addressBookValue = $this->content->getFieldValue('address_book');

        return $addressBookValue->destinationContentId;
    }

    public function getMembersId(): ?int
    {
        /** @var \Ibexa\Core\FieldType\Relation\Value $membersValue */
        $membersValue = $this->content->getFieldValue('members');

        return $membersValue->destinationContentId;
    }

    public function getLocation(): ?Location
    {
        if ($this->location === null) {
            $this->location = $this->getContent()->getVersionInfo()->getContentInfo()->getMainLocation();
        }

        return $this->location;
    }

    public function getLocationPath(): string
    {
        $location = $this->getLocation();

        if ($location === null) {
            throw new BadStateException('mainLocation', 'Company must have location');
        }

        return $location->pathString;
    }

    public function isActive(): bool
    {
        $activeValue = $this->getContent()->getFieldValue('active');
        if ($activeValue instanceof CheckboxValue) {
            return $activeValue->bool;
        }

        return false;
    }

    public function getCustomerGroup(): CustomerGroupInterface
    {
        /** @var \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value $customerGroupValue */
        $customerGroupValue = $this->content->getFieldValue('customer_group');

        $customerGroup = $customerGroupValue->getCustomerGroup();

        if ($customerGroup === null) {
            throw new BadStateException(
                'customerGroup',
                'Company must have customer group assigned'
            );
        }

        return $customerGroup;
    }
}
