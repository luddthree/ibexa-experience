<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency;

use Ibexa\Contracts\CorePersistence\HandlerInterface as CoreHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency;
use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyUpdateStruct;

/**
 * @extends \Ibexa\Contracts\CorePersistence\HandlerInterface<
 *     \Ibexa\ProductCatalog\Local\Persistence\Values\Currency
 * >
 */
interface HandlerInterface extends CoreHandlerInterface
{
    public function countAll(): int;

    public function create(CurrencyCreateStruct $struct): Currency;

    public function delete(int $id): void;

    public function update(CurrencyUpdateStruct $struct): Currency;
}
