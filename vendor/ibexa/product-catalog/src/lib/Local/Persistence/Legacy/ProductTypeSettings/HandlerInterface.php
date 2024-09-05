<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings;

use Ibexa\Contracts\CorePersistence\HandlerInterface as CoreHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSetting;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSettingCreateStruct;

/**
 * @internal
 *
 * @extends \Ibexa\Contracts\CorePersistence\HandlerInterface<\Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSetting>
 */
interface HandlerInterface extends CoreHandlerInterface
{
    public function create(ProductTypeSettingCreateStruct $createStruct): int;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function findByFieldDefinitionId(int $fieldDefinitionId, int $status): ProductTypeSetting;

    public function deleteByFieldDefinitionId(int $fieldDefinitionId, int $status): void;

    public function publish(int $fieldDefinitionId): void;
}
