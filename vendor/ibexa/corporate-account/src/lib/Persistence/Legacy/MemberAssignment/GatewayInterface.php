<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\MemberAssignment;

use Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface as CoreGatewayInterface;
use Ibexa\CorporateAccount\Persistence\Values\MemberAssignmentCreateStruct;
use Ibexa\CorporateAccount\Persistence\Values\MemberAssignmentUpdateStruct;

/**
 * @phpstan-type Data array{
 *     id: int,
 *     member_id: int,
 *     company_id: int,
 *     company_location_id: int,
 *     member_role: string,
 *     member_role_assignment_id: int
 * }
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface<Data>
 */
interface GatewayInterface extends CoreGatewayInterface
{
    /**
     * @phpstan-return Data
     */
    public function create(MemberAssignmentCreateStruct $struct): array;

    public function delete(int $id): void;

    /**
     * @phpstan-return Data
     */
    public function update(MemberAssignmentUpdateStruct $struct): array;
}
