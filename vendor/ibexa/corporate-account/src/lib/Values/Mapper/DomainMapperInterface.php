<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Values\Mapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;

interface DomainMapperInterface
{
    public function mapCompany(
        Content $content
    ): Company;

    public function mapMember(
        User $user,
        Company $company,
        Role $role
    ): Member;

    public function mapShippingAddress(
        Content $content
    ): ShippingAddress;

    public function mapApplication(
        Content $content
    ): Application;
}
