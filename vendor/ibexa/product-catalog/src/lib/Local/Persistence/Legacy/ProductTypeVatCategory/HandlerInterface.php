<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeVatCategory;

use Ibexa\Contracts\CorePersistence\HandlerInterface as CoreHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeVatCategoryCreateStruct;

/**
 * @extends \Ibexa\Contracts\CorePersistence\HandlerInterface<
 *     \Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeVatCategory
 * >
 */
interface HandlerInterface extends CoreHandlerInterface
{
    /**
     * @return array<\Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeVatCategory>
     */
    public function findByFieldDefinitionId(int $fieldDefinitionId, int $status): array;

    public function deleteByFieldDefinitionId(int $fieldDefinitionId, int $status): void;

    public function create(ProductTypeVatCategoryCreateStruct $createStruct): int;

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function delete(int $id): void;

    /**
     * @param array<string, mixed> $criteria
     */
    public function deleteBy(array $criteria): void;
}
