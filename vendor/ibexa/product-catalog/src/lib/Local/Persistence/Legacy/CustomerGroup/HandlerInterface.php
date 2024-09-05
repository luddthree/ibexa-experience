<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup;

use Ibexa\Contracts\CorePersistence\HandlerInterface as CoreHandlerInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;

/**
 * @extends \Ibexa\Contracts\CorePersistence\HandlerInterface<
 *     \Ibexa\ProductCatalog\Local\Persistence\Values\CustomerGroup
 * >
 */
interface HandlerInterface extends CoreHandlerInterface
{
    public function countAll(): int;

    public function create(CustomerGroupCreateStruct $createStruct): int;

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function update(CustomerGroupUpdateStruct $updateStruct): void;

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function delete(int $id): void;

    public function deleteTranslation(CustomerGroupInterface $customerGroup, string $languageCode): void;
}
