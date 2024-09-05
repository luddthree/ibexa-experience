<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Migrations\Generator\Company\Reference;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\CorporateAccount\Migrations\StepExecutor\ReferenceDefinition\AddressBookIdResolver;
use Ibexa\CorporateAccount\Migrations\StepExecutor\ReferenceDefinition\AddressBookLocationIdResolver;
use Ibexa\CorporateAccount\Migrations\StepExecutor\ReferenceDefinition\MembersGroupIdResolver;
use Ibexa\CorporateAccount\Migrations\StepExecutor\ReferenceDefinition\MembersGroupLocationIdResolver;
use Ibexa\Migration\Generator\Reference\AbstractReferenceGenerator;
use Ibexa\Migration\Generator\Reference\ReferenceMetadata;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\Content\ContentIdResolver;
use Webmozart\Assert\Assert;

final class ReferenceGenerator extends AbstractReferenceGenerator
{
    /**
     * @param \Ibexa\Contracts\CorporateAccount\Values\Company $company
     */
    public function generate(ValueObject $company): array
    {
        Assert::isInstanceOf($company, Company::class);

        $companyContentType = $company->getContent()->getContentType()->identifier;

        return $this->generateReferences($companyContentType, $company->getName());
    }

    protected function getReferenceMetadata(): array
    {
        return [
            new ReferenceMetadata('ref__members_group_id', MembersGroupIdResolver::getHandledType()),
            new ReferenceMetadata('ref__members_group_location_id', MembersGroupLocationIdResolver::getHandledType()),
            new ReferenceMetadata('ref__address_book_id', AddressBookIdResolver::getHandledType()),
            new ReferenceMetadata('ref__address_book_location_id', AddressBookLocationIdResolver::getHandledType()),
            new ReferenceMetadata('ref__company_id', ContentIdResolver::getHandledType()),
        ];
    }
}
