<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Application;

use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\CorporateAccount\Values\CompanyCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class MapCompanyCreateStructEvent extends Event
{
    private CompanyCreateStruct $companyCreateStruct;

    private Application $application;

    private CustomerGroupInterface $customerGroup;

    private int $salesRepId;

    public function __construct(
        CompanyCreateStruct $companyCreateStruct,
        Application $application,
        CustomerGroupInterface $customerGroup,
        int $salesRepId
    ) {
        $this->companyCreateStruct = $companyCreateStruct;
        $this->application = $application;
        $this->customerGroup = $customerGroup;
        $this->salesRepId = $salesRepId;
    }

    public function getCompanyCreateStruct(): CompanyCreateStruct
    {
        return $this->companyCreateStruct;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getCustomerGroup(): CustomerGroupInterface
    {
        return $this->customerGroup;
    }

    public function setCompanyCreateStruct(CompanyCreateStruct $companyCreateStruct): void
    {
        $this->companyCreateStruct = $companyCreateStruct;
    }

    public function getSalesRepId(): int
    {
        return $this->salesRepId;
    }
}
