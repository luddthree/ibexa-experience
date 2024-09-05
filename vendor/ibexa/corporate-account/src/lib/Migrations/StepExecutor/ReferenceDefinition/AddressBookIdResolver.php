<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Migrations\StepExecutor\ReferenceDefinition;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;
use Webmozart\Assert\Assert;

final class AddressBookIdResolver
{
    public static function getHandledType(): string
    {
        return 'address_book_id';
    }

    public function resolve(ReferenceDefinition $referenceDefinition, Company $company): Reference
    {
        $name = $referenceDefinition->getName();
        Assert::notNull(
            $company->getAddressBookId(),
            'Company object does not contain an address book ID. Make sure to reload Company object if persisted.'
        );

        return Reference::create($name, $company->getAddressBookId());
    }
}
