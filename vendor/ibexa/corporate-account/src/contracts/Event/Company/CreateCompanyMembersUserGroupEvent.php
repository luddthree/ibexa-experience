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

final class CreateCompanyMembersUserGroupEvent extends Event
{
    private Content $content;

    private Company $company;

    public function __construct(
        Content $content,
        Company $company
    ) {
        $this->content = $content;
        $this->company = $company;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }
}
