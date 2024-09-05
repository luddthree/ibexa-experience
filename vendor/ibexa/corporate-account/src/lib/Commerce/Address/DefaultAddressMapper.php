<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Commerce\Address;

use Ibexa\Bundle\Commerce\Eshop\Entities\Messages\Document\Contact;
use Ibexa\Bundle\Commerce\Eshop\Entities\Messages\Document\Party;
use Ibexa\Bundle\Commerce\Eshop\Entities\Messages\Document\PartyPartyIdentification;
use Ibexa\Bundle\Commerce\Eshop\Entities\Messages\Document\PartyPartyName;
use Ibexa\Bundle\Commerce\Eshop\Entities\Messages\Document\PartyPostalAddress;
use Ibexa\Bundle\Commerce\Eshop\Entities\Messages\Document\PartyPostalAddressAddressLine;
use Ibexa\Bundle\Commerce\Eshop\Entities\Messages\Document\PartyPostalAddressCountry;
use Ibexa\Bundle\Commerce\Eshop\Entities\Messages\Document\StringObject;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\CorporateAccount\Commerce\Address\AddressMapper;
use Ibexa\FieldTypeAddress\FieldType\Value;
use Symfony\Component\Intl\Countries;

final class DefaultAddressMapper implements AddressMapper
{
    public function mapAddressFieldTypeToCommerce(
        string $newAddressIdentifier,
        Party $existingParty,
        Value $sourceAddress,
        Contact $contact
    ): Party {
        $mappedParty = clone $existingParty;
        $partyName = new PartyPartyName();
        $partyName->Name = $this->createStringObject($sourceAddress->name);
        $mappedParty->PartyName[] = $partyName;

        $partyId = new PartyPartyIdentification();
        $partyId->ID = $this->createStringObject($newAddressIdentifier);
        $mappedParty->PartyIdentification[] = $partyId;

        $mappedParty->Contact = $contact;

        $postalAddress = new PartyPostalAddress();

        $addressLine = new PartyPostalAddressAddressLine();
        $postalAddress->AddressLine = [$addressLine];

        $postalAddress->StreetName = $this->createStringObject($sourceAddress->fields['street'] ?? '');
        $postalAddress->CityName = $this->createStringObject($sourceAddress->fields['locality'] ?? '');
        $postalAddress->PostalZone = $this->createStringObject($sourceAddress->fields['postal_code'] ?? '');
        $postalAddress->CountrySubentity = $this->createStringObject($sourceAddress->fields['region'] ?? '');

        $mappedParty->PostalAddress = $postalAddress;

        if ($sourceAddress->country !== null) {
            $country = new PartyPostalAddressCountry();
            $country->IdentificationCode = $this->createStringObject($sourceAddress->country);
            $country->Name = $this->createStringObject(Countries::getName($sourceAddress->country));

            $postalAddress->Country = $country;
        }

        return $mappedParty;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $contactContent
     */
    public function mapContactToCommerce(
        ValueObject $contactContent
    ): Contact {
        $contact = new Contact();

        /** @var \Ibexa\Core\FieldType\EmailAddress\Value $emailValue */
        $emailValue = $contactContent->getFieldValue('email');
        $contact->ElectronicMail = $this->createStringObject($emailValue->email);

        /** @var \Ibexa\Core\FieldType\TextLine\Value $phoneValue */
        $phoneValue = $contactContent->getFieldValue('phone');
        $contact->Telephone = $this->createStringObject($phoneValue->text);

        return $contact;
    }

    private function createStringObject(?string $value): StringObject
    {
        $obj = new StringObject();
        $obj->value = (string)$value;

        return $obj;
    }
}
