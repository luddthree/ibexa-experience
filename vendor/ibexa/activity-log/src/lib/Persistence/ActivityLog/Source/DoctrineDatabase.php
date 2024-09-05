<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence\ActivityLog\Source;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataInterface;
use LogicException;

/**
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\Source\GatewayInterface
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<Data>
 */
final class DoctrineDatabase extends AbstractDoctrineDatabase implements GatewayInterface
{
    protected function getTableName(): string
    {
        return StorageSchema::TABLE_NAME;
    }

    protected function buildMetadata(): DoctrineSchemaMetadataInterface
    {
        $columns = [
            StorageSchema::COLUMN_ID => Types::INTEGER,
            StorageSchema::COLUMN_NAME => Types::STRING,
        ];

        $metadata = new DoctrineSchemaMetadata(
            $this->connection,
            GroupSource::class,
            $this->getTableName(),
            $columns,
            [StorageSchema::COLUMN_ID],
        );

        return $metadata;
    }

    public function save(string $source): int
    {
        return $this->doInsert([
            StorageSchema::COLUMN_NAME => $source,
        ]);
    }

    public function findOrCreate(string $source): array
    {
        $result = $this->findOneBy([StorageSchema::COLUMN_NAME => $source]);
        if ($result !== null) {
            return $result;
        }

        $this->save($source);
        $result = $this->findOneBy([StorageSchema::COLUMN_NAME => $source]);

        if ($result === null) {
            throw new LogicException('Expected source data to be available after inserting it.');
        }

        return $result;
    }
}
