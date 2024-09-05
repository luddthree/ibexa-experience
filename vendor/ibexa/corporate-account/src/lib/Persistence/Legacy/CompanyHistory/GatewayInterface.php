<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\CompanyHistory;

use Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface as CoreGatewayInterface;
use Ibexa\CorporateAccount\Persistence\Values\CompanyHistoryCreateStruct;
use Ibexa\CorporateAccount\Persistence\Values\CompanyHistoryUpdateStruct;

/**
 * @phpstan-type Data = array{
 *     id: int,
 *     application_id: int,
 *     company_id: ?int,
 *     user_id: ?int,
 *     created: \DateTimeImmutable,
 *     event_name: string,
 *     event_data: null|array<string, mixed>
 * }
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface<Data>
 */
interface GatewayInterface extends CoreGatewayInterface
{
    /**
     * @phpstan-return Data
     */
    public function create(CompanyHistoryCreateStruct $struct): array;

    public function delete(int $id): void;

    /**
     * @phpstan-return Data
     */
    public function update(CompanyHistoryUpdateStruct $struct): array;
}
