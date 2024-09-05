<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\ApplicationState;

use Ibexa\Contracts\CorePersistence\HandlerInterface as CoreHandlerInterface;
use Ibexa\CorporateAccount\Persistence\Values\ApplicationState;
use Ibexa\CorporateAccount\Persistence\Values\ApplicationStateCreateStruct;
use Ibexa\CorporateAccount\Persistence\Values\ApplicationStateUpdateStruct;

/**
 * @extends \Ibexa\Contracts\CorePersistence\HandlerInterface<
 *     \Ibexa\CorporateAccount\Persistence\Values\ApplicationState
 * >
 */
interface HandlerInterface extends CoreHandlerInterface
{
    public function countAll(): int;

    public function create(ApplicationStateCreateStruct $struct): ApplicationState;

    public function delete(int $id): void;

    public function update(ApplicationStateUpdateStruct $struct): ApplicationState;
}
