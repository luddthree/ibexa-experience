<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\CompanyHistory;

use Ibexa\Contracts\CorePersistence\HandlerInterface as CoreHandlerInterface;
use Ibexa\CorporateAccount\Persistence\Values\CompanyHistory;
use Ibexa\CorporateAccount\Persistence\Values\CompanyHistoryCreateStruct;
use Ibexa\CorporateAccount\Persistence\Values\CompanyHistoryUpdateStruct;

/**
 * @extends \Ibexa\Contracts\CorePersistence\HandlerInterface<
 *     \Ibexa\CorporateAccount\Persistence\Values\CompanyHistory
 * >
 */
interface HandlerInterface extends CoreHandlerInterface
{
    public function countAll(): int;

    public function create(CompanyHistoryCreateStruct $struct): CompanyHistory;

    public function delete(int $id): void;

    public function update(CompanyHistoryUpdateStruct $struct): CompanyHistory;
}
