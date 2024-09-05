<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Product\Gateway;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineRelationship;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Product\GatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Product;

/**
 * @phpstan-import-type Data from \Ibexa\ProductCatalog\Local\Persistence\Legacy\Product\GatewayInterface
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<Data>
 */
final class DoctrineDatabase extends AbstractDoctrineDatabase implements GatewayInterface
{
    protected function getTableName(): string
    {
        return StorageSchema::TABLE_NAME;
    }

    protected function buildMetadata(): DoctrineSchemaMetadata
    {
        $columns = [
            StorageSchema::COLUMN_ID => Types::INTEGER,
            StorageSchema::COLUMN_CODE => Types::STRING,
            StorageSchema::COLUMN_BASE_PRODUCT_ID => Types::INTEGER,
            StorageSchema::COLUMN_IS_PUBLISHED => Types::BOOLEAN,
        ];

        $metadata = new DoctrineSchemaMetadata(
            $this->connection,
            Product::class,
            $this->getTableName(),
            $columns,
            ['id'],
        );

        $metadata->addRelationship(new DoctrineRelationship(
            Product::class,
            'baseProduct',
            StorageSchema::COLUMN_BASE_PRODUCT_ID,
            StorageSchema::COLUMN_ID,
        ));

        return $metadata;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function insert(string $code, ?int $baseProductId = null): int
    {
        return $this->doInsert([
            StorageSchema::COLUMN_CODE => $code,
            StorageSchema::COLUMN_BASE_PRODUCT_ID => $baseProductId,
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(string $originalCode, ?string $newCode, ?bool $isPublished): void
    {
        $criteria = [
            StorageSchema::COLUMN_CODE => $originalCode,
        ];

        $data = [
            StorageSchema::COLUMN_CODE => $newCode,
            StorageSchema::COLUMN_IS_PUBLISHED => $isPublished,
        ];
        $data = array_filter($data);

        if (empty($data)) {
            return;
        }

        $this->doUpdate($criteria, $data);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findOneByCode(string $code): ?array
    {
        return $this->findOneBy([StorageSchema::COLUMN_CODE => $code]);
    }
}
