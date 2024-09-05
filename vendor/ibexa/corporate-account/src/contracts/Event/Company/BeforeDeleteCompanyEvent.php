<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Company;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Symfony\Contracts\EventDispatcher\Event;

final class BeforeDeleteCompanyEvent extends Event
{
    private Company $company;

    public function __construct(
        Company $company
    ) {
        $this->company = $company;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }
}
