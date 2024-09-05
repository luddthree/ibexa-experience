<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\User\Role as APIRole;
use Ibexa\Contracts\CorporateAccount\Values\MemberCreateStruct as APIMemberCreateStruct;
use Ibexa\Rest\Value as RestValue;

/**
 * @internal
 */
final class MemberCreateStruct extends RestValue
{
    public APIMemberCreateStruct $memberCreateStruct;

    public APIRole $role;

    public function __construct(APIMemberCreateStruct $memberCreateStruct, APIRole $role)
    {
        $this->memberCreateStruct = $memberCreateStruct;
        $this->role = $role;
    }
}
