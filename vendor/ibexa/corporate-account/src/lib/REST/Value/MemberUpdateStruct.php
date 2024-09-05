<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\User\Role as APIRole;
use Ibexa\Contracts\CorporateAccount\Values\MemberUpdateStruct as APIMemberUpdateStruct;
use Ibexa\Rest\Value as RestValue;

/**
 * @internal
 */
final class MemberUpdateStruct extends RestValue
{
    public APIMemberUpdateStruct $memberUpdateStruct;

    public ?APIRole $newRole;

    public function __construct(APIMemberUpdateStruct $memberUpdateStruct, ?APIRole $newRole)
    {
        $this->memberUpdateStruct = $memberUpdateStruct;
        $this->newRole = $newRole;
    }
}
