<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence\ActivityLog\Action;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;

/**
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\Action\ActionGatewayInterface
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<Data>
 */
final class ActionGateway extends AbstractDoctrineDatabase implements ActionGatewayInterface
{
    protected function getTableName(): string
    {
        return StorageSchema::TABLE_NAME;
    }

    protected function buildMetadata(): DoctrineSchemaMetadata
    {
        $columns = [
            StorageSchema::COLUMN_ID => Types::INTEGER,
            StorageSchema::COLUMN_ACTION => Types::STRING,
        ];

        return new DoctrineSchemaMetadata(
            $this->connection,
            Action::class,
            $this->getTableName(),
            $columns,
            [
                StorageSchema::COLUMN_ID,
            ],
        );
    }

    public function save(string $action): int
    {
        return $this->doInsert([
            StorageSchema::COLUMN_ACTION => $action,
        ]);
    }
}
