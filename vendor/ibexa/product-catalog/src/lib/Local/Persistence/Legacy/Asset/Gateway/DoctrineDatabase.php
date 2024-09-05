<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Asset\Gateway;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Asset\GatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AssetCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AssetUpdateStruct;

/**
 * @phpstan-type Data array{
 *     id: int,
 *     product_specification_id: int,
 *     uri: non-empty-string,
 *     tags: string[]
 * }
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<Data>
 */
final class DoctrineDatabase extends AbstractDoctrineDatabase implements GatewayInterface
{
    protected function getTableName(): string
    {
        return Schema::TABLE_NAME;
    }

    public function findByProduct(int $productSpecificationId): array
    {
        $criteria = [
            Schema::COLUMN_PRODUCT_SPECIFICATION_ID => $productSpecificationId,
        ];

        $orderBy = [
            Schema::COLUMN_ID => 'ASC',
        ];

        return $this->findBy($criteria, $orderBy);
    }

    public function create(AssetCreateStruct $createStruct): int
    {
        return $this->doInsert([
            Schema::COLUMN_PRODUCT_SPECIFICATION_ID => $createStruct->productSpecificationId,
            Schema::COLUMN_URI => $createStruct->uri,
            Schema::COLUMN_TAGS => $createStruct->tags,
        ]);
    }

    public function update(AssetUpdateStruct $updateStruct): void
    {
        $data = [
            Schema::COLUMN_URI => $updateStruct->uri,
            Schema::COLUMN_TAGS => $updateStruct->tags,
        ];

        $criteria = [
            Schema::COLUMN_ID => $updateStruct->id,
        ];

        $this->doUpdate($criteria, $data);
    }

    public function delete(int $id): void
    {
        $this->doDelete([
            Schema::COLUMN_ID => $id,
        ]);
    }

    protected function buildMetadata(): DoctrineSchemaMetadata
    {
        return new DoctrineSchemaMetadata(
            $this->connection,
            AssetInterface::class,
            $this->getTableName(),
            [
                Schema::COLUMN_ID => Types::INTEGER,
                Schema::COLUMN_PRODUCT_SPECIFICATION_ID => Types::INTEGER,
                Schema::COLUMN_URI => Types::STRING,
                Schema::COLUMN_TAGS => Types::JSON,
            ],
            [Schema::COLUMN_ID]
        );
    }
}
