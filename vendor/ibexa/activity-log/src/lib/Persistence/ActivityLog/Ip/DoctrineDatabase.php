<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence\ActivityLog\Ip;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataInterface;
use LogicException;

/**
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\Ip\GatewayInterface
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
            StorageSchema::COLUMN_IP => Types::STRING,
        ];

        $metadata = new DoctrineSchemaMetadata(
            $this->connection,
            Ip::class,
            $this->getTableName(),
            $columns,
            [StorageSchema::COLUMN_ID],
        );

        return $metadata;
    }

    public function save(string $ip): int
    {
        return $this->doInsert([
            StorageSchema::COLUMN_IP => $ip,
        ]);
    }

    public function findOrCreate(string $ip): array
    {
        $result = $this->findOneBy([StorageSchema::COLUMN_IP => $ip]);
        if ($result !== null) {
            return $result;
        }

        $this->save($ip);
        $result = $this->findOneBy([StorageSchema::COLUMN_IP => $ip]);

        if ($result === null) {
            throw new LogicException('Expected IP data to be available after inserting it.');
        }

        return $result;
    }
}
