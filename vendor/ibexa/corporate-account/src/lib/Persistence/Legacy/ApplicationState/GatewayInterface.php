<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\ApplicationState;

use Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface as CoreGatewayInterface;
use Ibexa\CorporateAccount\Persistence\Values\ApplicationStateCreateStruct;
use Ibexa\CorporateAccount\Persistence\Values\ApplicationStateUpdateStruct;

/**
 * @phpstan-type Data = array{
 *     id: int,
 *     application_id: int,
 *     state: string,
 *     company_id: ?int
 * }
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface<Data>
 */
interface GatewayInterface extends CoreGatewayInterface
{
    /**
     * @phpstan-return Data
     */
    public function create(ApplicationStateCreateStruct $struct): array;

    public function delete(int $id): void;

    /**
     * @phpstan-return Data
     */
    public function update(ApplicationStateUpdateStruct $struct): array;
}
