<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Company;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Symfony\Contracts\EventDispatcher\Event;

final class BeforeSetContactEvent extends Event
{
    private Company $company;

    private Member $contact;

    public function __construct(
        Company $company,
        Member $contact
    ) {
        $this->company = $company;
        $this->contact = $contact;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getContact(): Member
    {
        return $this->contact;
    }
}
