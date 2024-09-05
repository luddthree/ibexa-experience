<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Commerce\Address;

use Ibexa\Bundle\Commerce\Eshop\Entities\Messages\Document\Contact;
use Ibexa\Bundle\Commerce\Eshop\Entities\Messages\Document\Party;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\FieldTypeAddress\FieldType\Value;

interface AddressMapper
{
    public function mapAddressFieldTypeToCommerce(
        string $newAddressIdentifier,
        Party $existingParty,
        Value $sourceAddress,
        Contact $contact
    ): Party;

    public function mapContactToCommerce(
        ValueObject $contactContent
    ): Contact;
}
