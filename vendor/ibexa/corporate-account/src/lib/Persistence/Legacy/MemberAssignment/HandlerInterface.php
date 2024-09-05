<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\MemberAssignment;

use Ibexa\Contracts\CorePersistence\HandlerInterface as CoreHandlerInterface;
use Ibexa\CorporateAccount\Persistence\Values\MemberAssignment;
use Ibexa\CorporateAccount\Persistence\Values\MemberAssignmentCreateStruct;
use Ibexa\CorporateAccount\Persistence\Values\MemberAssignmentUpdateStruct;

/**
 * @extends \Ibexa\Contracts\CorePersistence\HandlerInterface<
 *     \Ibexa\CorporateAccount\Persistence\Values\MemberAssignment
 * >
 */
interface HandlerInterface extends CoreHandlerInterface
{
    public function countAll(): int;

    public function create(MemberAssignmentCreateStruct $struct): MemberAssignment;

    public function delete(int $id): void;

    public function update(MemberAssignmentUpdateStruct $struct): MemberAssignment;
}
