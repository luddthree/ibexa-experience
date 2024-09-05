<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Application;

use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddressUpdateStruct;
use Symfony\Contracts\EventDispatcher\Event;

final class MapShippingAddressUpdateStructEvent extends Event
{
    private ShippingAddressUpdateStruct $companyUpdateStruct;

    private Application $application;

    public function __construct(
        ShippingAddressUpdateStruct $companyUpdateStruct,
        Application $application
    ) {
        $this->companyUpdateStruct = $companyUpdateStruct;
        $this->application = $application;
    }

    public function getShippingAddressUpdateStruct(): ShippingAddressUpdateStruct
    {
        return $this->companyUpdateStruct;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function setShippingAddressUpdateStruct(ShippingAddressUpdateStruct $companyUpdateStruct): void
    {
        $this->companyUpdateStruct = $companyUpdateStruct;
    }
}
