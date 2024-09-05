<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Data\Company;

final class CompanyBulkDeactivateData
{
    /** @var \Ibexa\Contracts\CorporateAccount\Values\Company[] */
    private array $companies;

    /** @param \Ibexa\Contracts\CorporateAccount\Values\Company[] $companies */
    public function __construct(array $companies = [])
    {
        $this->companies = $companies;
    }

    /** @return \Ibexa\Contracts\CorporateAccount\Values\Company[] */
    public function getCompanies(): array
    {
        return $this->companies;
    }

    /** @param \Ibexa\Contracts\CorporateAccount\Values\Company[] $companies */
    public function setCompanies(array $companies): void
    {
        $this->companies = $companies;
    }
}
