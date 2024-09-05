<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog;

use Ibexa\Contracts\CorePersistence\HandlerInterface as CoreHandlerInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Catalog;
use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogCopyStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogUpdateStruct;

/**
 * @extends \Ibexa\Contracts\CorePersistence\HandlerInterface<
 *     \Ibexa\ProductCatalog\Local\Persistence\Values\Catalog
 * >
 */
interface HandlerInterface extends CoreHandlerInterface
{
    public function countAll(): int;

    public function create(CatalogCreateStruct $createStruct): int;

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function update(CatalogUpdateStruct $updateStruct): void;

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function delete(int $id): void;

    public function copy(CatalogCopyStruct $copyStruct): int;

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, mixed>|null $orderBy
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?Catalog;

    public function deleteTranslation(CatalogInterface $catalog, string $languageCode): void;
}
