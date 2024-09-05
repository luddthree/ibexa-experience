<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Company;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Symfony\Contracts\EventDispatcher\Event;

final class SetCompanyAddressBookRelationEvent extends Event
{
    private Company $company;

    private Content $content;

    public function __construct(
        Company $company,
        Content $content
    ) {
        $this->company = $company;
        $this->content = $content;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getContent(): Content
    {
        return $this->content;
    }
}
